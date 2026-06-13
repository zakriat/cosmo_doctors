<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Appointment\Models\BillingRecord;
class AppointmentResource extends JsonResource
{
    use AppointmentTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $remaining_payable_amount = 0;
        $cacellationcharges = 0;
        $startTime = Carbon::parse($this->appointment_time);
        $endTime = $startTime->copy()->addMinutes($this->duration);

        $tax_data = $this->calculateTaxAmounts($this->appointmenttransaction ? $this->appointmenttransaction->tax_percentage : null, ($this->service_amount));


        $totalAmount = $this->total_amount;

        $billing_record = optional($this->patientEncounter)->billingrecord ?? collect();
        $billingItems = optional(optional($this->patientEncounter)->billingrecord)->billingItem ?? collect();
        if ($billing_record instanceof BillingRecord) {
            $discount_type = $billing_record->final_discount_type ?? '';
            $total_discount = $billing_record->final_discount_value ?? 0;

            if ($discount_type === 'percentage') {
                $billing_discount_total_amount = $billingItems->sum('total_amount') * $total_discount / 100;
            } else {
                $billing_discount_total_amount = $total_discount;
            }

            $totl_amount = $billingItems->sum('total_amount') - $billing_discount_total_amount;
        }
        if ($billing_record instanceof BillingRecord) {
            $total_amount = $totl_amount;
            $tax_data = json_encode($tax_data);

            $tax_data = $this->calculateTaxAmounts($tax_data ?? null, $total_amount);

            $total_tax = array_sum(array_column($tax_data, 'amount'));

            $totalAmount = $total_amount + $total_tax ;
        }
        if (optional($this->appointmenttransaction)->advance_payment_status == 1) {
            $remaining_payable_amount = $totalAmount - $this->advance_paid_amount;
        }
        if($this->status == 'cancelled'){
            $cacellationcharges = $this->cancellation_charge_amount ?? 0;
        }elseif($this->status == 'pending' && isset($this->appointmenttransaction) && optional($this->appointmenttransaction)->transaction_type == 'cash' ){
            $cacellationcharges =  0;
        }else{
            $cacellationcharges = $this->getCancellationCharges();
        }

       
        return [
            'id' => $this->id,
            'status' => $this->status,
            'start_date_time' => $this->start_date_time,
            'user_id' => $this->user_id,
            'user_name' => trim(optional($this->user)->first_name . ' ' . optional($this->user)->last_name) ?: default_user_name(),
            'user_image' => optional($this->user)->profile_image ?: default_user_avatar(),
            'user_email' => optional($this->user)->email,
            'user_phone' => optional($this->user)->mobile,
            'country_id' => optional($this->user)->country,
            'state_id' => optional($this->user)->state,
            'city_id' => optional($this->user)->city,
            'country_name' => optional(optional($this->user)->countries)->name,
            'state_name' => optional(optional($this->user)->states)->name,
            'city_name' => optional(optional($this->user)->cities)->name,
            'address' => optional($this->user)->address,
            'pincode' => optional($this->user)->pincode,
            'clinic_id' => $this->clinic_id,
            'clinic_name' => optional($this->cliniccenter)->name,
            'doctor_id' => $this->doctor_id,
            'doctor_name' => trim(optional($this->doctor)->first_name . ' ' . optional($this->doctor)->last_name) ?: default_user_name(),
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'description' => $this->description,
            'encounter_id' => optional($this->patientEncounter)->id,
            'encounter_description' => optional($this->patientEncounter)->description,
            'encounter_status' => optional($this->patientEncounter)->status,
            'end_time' => $endTime->format('H:i:s'),
            'duration' => $this->duration,
            'service_id' => $this->service_id,
            'service_name' => optional($this->clinicservice)->name,
            'service_type' => optional($this->clinicservice)->type,
            'is_video_consultancy' => optional($this->clinicservice)->is_video_consultancy,
            'appointment_extra_info' => $this->appointment_extra_info,
            'total_amount' => $totalAmount,
            'service_amount' => $this->service_amount,
            'payment_status' => optional($this->appointmenttransaction)->payment_status,
            'is_enable_advance_payment' => optional($this->clinicservice)->is_enable_advance_payment,
            'advance_payment_amount' => $this->advance_payment_amount,
            'advance_payment_status' => optional($this->appointmenttransaction)->advance_payment_status,
            'advance_paid_amount' => $this->advance_paid_amount,
            'remaining_payable_amount' => $remaining_payable_amount,
            'google_link' => $this->meet_link,
            'zoom_link' => $this->start_video_link,
            'book_for_image'=> optional($this->otherPatient)->profile_image ?: default_user_avatar(),
            'book_for_name'=> optional($this->otherPatient)->full_name??null,
            'cancellation_charge' => $this->cancellation_charge ?? (int) setting('cancellation_charge'),
            'cancellation_type' => $this->cancellation_type ?? setting('cancellation_type'),
            'cancellation_charge_amount' => $cacellationcharges,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
