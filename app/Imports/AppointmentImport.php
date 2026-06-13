<?php

namespace App\Imports;

use Modules\Appointment\Models\Appointment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Modules\Clinic\Models\ClinicsService;
use App\Models\User;
use Modules\Clinic\Models\Clinics;
use Modules\Tax\Models\Tax;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Models\WalletHistory;
use Modules\Appointment\Models\AppointmentTransaction;
use Illuminate\Http\Request;
use Modules\Appointment\Models\BillingRecord;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Appointment\Trait\BillingRecordTrait;
use Modules\Appointment\Trait\EncounterTrait;
class AppointmentImport implements ToModel, WithHeadingRow
{
    use EncounterTrait;
    use BillingRecordTrait;

    private $importedAppointments = []; // Store successfully imported appointments
    private $errors = [];

    public function model(array $row)
    {

        $requiredFields = ['patient_name', 'start_date_time', 'services', 'doctor', 'status', 'clinic_name'];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($row[$field])) {
                $missingFields[] = $field;
            }
        }
        if (!empty($missingFields)) {
            $this->errors[] = [
                'row' => $row,
                'message' => 'Missing required fields: ' . implode(', ', $missingFields),
            ];
            return;
        }
        $mappedRow = $this->transformRow($row);

        $appointment = Appointment::create($mappedRow);

        $request = new Request([
            'id' => $appointment->id,
            'transaction_type' => $row['transaction_type'] ?? 'cash',
            'payment_status' => $row['payment_status'] ?? 0,
            'advance_payment_status' => $row['advance_payment_status'] ?? 0,
            'advance_payment_amount' => $row['advance_payment_amount'] ?? 0,
            'remaining_payment_amount' => $row['remaining_payment_amount'] ?? 0,
            'tax_percentage' => $row['tax_percentage'] ?? 0,
            'tip' => $row['tip'] ?? 0,
        ]);
        $this->savePayment($request);
        $this->handleStatus($appointment, $row['status'] ?? 'pending');
        $this->importedAppointments[] = [
            'appointment' => $appointment,
            'appointment_data' => $row,
        ];
    }

    private function transformRow(array $row)
    {
        $startDateTime = isset($row['start_date_time'])
            ? Carbon::parse($row['start_date_time'])
            : now();
        $serviceAmount = $this->getServiceDataByName($row['services'] ?? null)['service_amount'];
        $taxList = Tax::active()
            ->whereNull('module_type')
            ->orWhere('module_type', 'services')
            ->get();

        $taxDetails = [];
        $totalTaxAmount = 0;
        foreach ($taxList as $tax) {
            if (isset($tax->tax_type) && $tax->tax_type === 'exclusive' || isset($tax->tax_scope) && $tax->tax_scope === 'exclusive') {

                if ($tax['type'] === 'percent') {
                    $taxAmount = $serviceAmount * $tax['value'] / 100;
                } elseif ($tax['type'] === 'fixed') {
                    $taxAmount = $tax['value'];
                }

                $taxDetails[] = [
                    'tax_name' => $tax['title'],
                    'tax_type' => $tax['type'],
                    'tax_value' => $tax['type'] === 'percent' ? $tax['value'] : $tax['value'],
                    'tax_amount' => $taxAmount,
                ];
                $totalTaxAmount += $taxAmount;
            }

        }

        // Add tax amount to the total amount
        $totalAmountWithTax = $serviceAmount + $totalTaxAmount;
        return [
            'start_date_time' => $startDateTime->format('Y-m-d H:i:s'),
            'appointment_date' => $startDateTime->toDateString(),
            'appointment_time' => $startDateTime->toTimeString(),
            'user_id' => $this->getUserName($row['patient_name'], 'user') ?? 1,
            'clinic_id' => $this->getClinicName($row['clinic_name']) ?? 1,
            'doctor_id' => $this->getUserName($row['doctor'], 'doctor') ?? 1,
            'service_id' => $this->getServiceDataByName($row['services'] ?? null)['service_id'],
            'service_amount' => $serviceAmount ?? 0,
            'service_price' => $serviceAmount ?? 0,
            'total_amount' => $totalAmountWithTax ?? 0,
            'duration' => $this->getServiceDataByName($row['services'] ?? null)['duration'],
            'advance_payment_amount' => $row['advance_payment_amount'] ?? 0,
            'advance_paid_amount' => $row['advance_paid_amount'] ?? 0,
            'status' => $row['status'] ?? 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function handleStatus(Appointment $appointment, string $status)
    {
        switch ($status) {
            case 'check_in':
                $this->generateEncounter($appointment);
                break;
            case 'checkout':
                $this->handleCheckout($appointment);
                break;
            case 'cancelled':
                $this->handleCancellation($appointment);
                break;
            case 'confirmed':
                $this->handleCheckout($appointment);
                break;
        }
    }

    private function savePayment(Request $request)
    {
        $data = $request->all();
        $data['tip_amount'] = $data['tip'] ?? 0;
        $appointment = Appointment::findOrFail($data['id']);
        $tax = Tax::active()
            ->whereNull('module_type')
            ->where('tax_type','exclusive')
            ->orWhere('module_type', 'services')
            ->where('status', 1)
            ->get();
        $servicePrice = $appointment->service_amount;
        $tax = $tax->filter(function ($taxItem) use ($servicePrice) {
            if (
                (isset($taxItem->tax_type) && $taxItem->tax_type === 'inclusive') ||
                (isset($taxItem->tax_scope) && $taxItem->tax_scope === 'inclusive')
            ) {
                $inclusiveTaxAmount = 0;
                if (isset($taxItem->tax_type) && $taxItem->tax_type === 'percent') {
                    $inclusiveTaxAmount = ($servicePrice * $taxItem->value) / 100;
                } else {
                    $inclusiveTaxAmount = $taxItem->value;
                }
                if ($inclusiveTaxAmount > $servicePrice) {
                    return false; // Exclude this tax item
                }
                return true;
            }
            return true;
        });
        
        $paymentStatus = match (strtolower($data['payment_status'] ?? 'pending')) {
            'paid' => 1,
            'pending' => 0,
            default => 0,
        };

        $transactionData = [
            'appointment_id' => $appointment->id,
            'transaction_type' => $data['transaction_type'] ?? 'cash',
            'total_amount' => $appointment->service_amount,
            'payment_status' => $paymentStatus,
            'discount_value' => $data['discount_value'] ?? 0,
            'discount_type' => $data['discount_type'] ?? null,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'tax_percentage' => json_encode($tax),
            'tip_amount' => $data['tip_amount'] ?? 0,
        ];

        if ($data['transaction_type'] === 'wallet') {
            $this->handleWalletPayment($appointment);
        }

        $appointmentTransaction = AppointmentTransaction::create($transactionData);

        if ($appointmentTransaction && $data['advance_payment_status'] == 1) {
            $appointment->advance_paid_amount = $data['advance_payment_amount'];
            $appointment->save();

            $appointmentTransaction->advance_payment_status = $data['advance_payment_status'];
            $appointmentTransaction->total_amount = $data['advance_payment_amount'];
            $appointmentTransaction->save();
        }
    }

    private function handleWalletPayment($appointment)
    {
        $wallet = Wallet::where('user_id', $appointment->user_id)->first();
        if ($wallet && $wallet->amount >= $appointment->total_amount) {
            $wallet->decrement('amount', $appointment->total_amount);
            WalletHistory::create([
                'user_id' => $wallet->user_id,
                'datetime' => now(),
                'activity_type' => 'paid_for_appointment',
                'activity_message' => "Paid for appointment #{$appointment->id}",
            ]);
        } else {
            throw new \Exception('Insufficient wallet balance.');
        }
    }

    private function getServiceDataByName($serviceName)
    {
        $service = ClinicsService::where('name', $serviceName)->first();
        return $service
            ? ['service_id' => $service->id, 'duration' => $service->duration_min, 'service_amount' => $service->charges]
            : ['service_id' => null, 'duration' => 30];
    }

    private function getUserName($userName, $type)
    {
        if ($userName) {
            $nameParts = explode(' ', $userName, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? null;

            $user = User::where('user_type', $type)
                ->where('first_name', $firstName)
                ->when($lastName, fn($query) => $query->where('last_name', $lastName))
                ->first();
            return $user?->id;
        }
        return null;
    }

    private function getClinicName($clinicName)
    {
        return Clinics::where('name', $clinicName)->first()?->id;
    }

    public function getImportedAppointments()
    {
        return $this->importedAppointments;
    }

    private function generateEncounter(Appointment $appointment)
    {
        $encounter = PatientEncounter::create([
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->user_id,
            'doctor_id' => $appointment->doctor_id,
            'clinic_id' => $appointment->clinic_id,
            'status' => 1, // Example status
            'start_time' => now(),
        ]);
        $encounter_details = PatientEncounter::where('id', $encounter->id)->with('appointment')->first();

        if ($encounter_details) {
            $billing_record = $this->generateBillingRecord($encounter_details);
            $billing_record = BillingRecord::where('id', $billing_record->id)->with('clinicservice', 'patientencounter')->first();
            if ($billing_record) {
                $billing_item = $this->generateBillingItem($billing_record);
            }
        }
        return $encounter;
    }

    private function handleCheckout(Appointment $appointment)
    {
        $encounter = PatientEncounter::where('appointment_id', $appointment->id)->first();
        if ($encounter) {
            $billingRecord = BillingRecord::where('encounter_id', $encounter->id)
                ->where('payment_status', 1) // Ensure payment is completed
                ->first();
            if ($billingRecord) {
                $encounter->update(['status' => 0]);
                Log::info("Encounter marked as completed for Appointment ID: {$appointment->id}");
            } else {
                throw new \Exception('Payment not completed. Please complete the payment before checkout.');
            }
        } else {
            $this->generateEncounter($appointment);
            $encounter = PatientEncounter::where('appointment_id', $appointment->id)->first();
            if ($encounter) {
                $billingRecord = BillingRecord::where('encounter_id', $encounter->id)
                    ->where('payment_status', 1) // Ensure payment is completed
                    ->first();
                if ($billingRecord) {
                    $encounter->update(['status' => 0]);
                }
            }
        }
    }

    private function handleCancellation(Appointment $appointment)
    {
        try {
            $userWallet = Wallet::where('user_id', $appointment->user_id)->first();
            if (!$userWallet) {
                Log::warning("Wallet not found for User ID: {$appointment->user_id}");
                return;
            }
            $advancePaidAmount = $appointment->advance_paid_amount;

            $refundAmount = $advancePaidAmount && optional($appointment->appointmentTransaction)->payment_status == 0
                ? $advancePaidAmount
                : $appointment->total_amount;

            $userWallet->amount += $refundAmount;
            $userWallet->save();
            WalletHistory::create([
                'user_id' => $userWallet->user_id,
                'datetime' => now(),
                'activity_type' => 'wallet_refund',
                'activity_message' => trans('messages.wallet_refund', ['value' => $appointment->id]),
                'activity_data' => json_encode([
                    'appointment_id' => $appointment->id,
                    'refund_amount' => $refundAmount,
                ]),
            ]);
        } catch (\Exception $e) {
            Log::error("Cancellation failed for Appointment ID: {$appointment->id}: {$e->getMessage()}");
            throw $e;
        }
    }


    private function handleComplete(Appointment $appointment)
    {

    }

    public function getErrors()
    {
        return $this->errors;
    }


}
