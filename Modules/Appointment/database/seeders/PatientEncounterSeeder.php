<?php

namespace Modules\Appointment\database\seeders;

use Illuminate\Database\Seeder;
use DateTime;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Appointment\Models\BillingRecord;
use Modules\Appointment\Models\BillingItem;
use Modules\Appointment\Trait\AppointmentTrait;

class PatientEncounterSeeder extends Seeder
{
    use AppointmentTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentDate = new DateTime();
        $onemonth = clone $currentDate;
        $onemonth->modify('1 month');
        
        $twomonth = clone $currentDate;
        $twomonth->modify('2 months');
        
        $beforeonemonth = clone $currentDate;
        $beforeonemonth->modify('-1 month');
        
        $beforetwomonth = clone $currentDate;
        $beforetwomonth->modify('-2 months');
        
        \DB::table('patient_encounters')->delete();
        
        $data = (array (
            0 => 
            array (
                'id' => 1,
                'encounter_date' => $beforeonemonth->format('Y-m-d H:i:s'),
                'user_id' => 14,
                'clinic_id' => 2,
                'doctor_id' => 18,
                'vendor_id' => NULL,
                'appointment_id' => 6,
                'encounter_template_id' => NULL,
                'description' => NULL,
                'status' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-05-27 05:57:00',
                'updated_at' => '2024-05-27 05:57:38',
            ),
            1 => 
            array (
                'id' => 2,
                'encounter_date' => $beforetwomonth->format('Y-m-d H:i:s'),
                'user_id' => 9,
                'clinic_id' => 1,
                'doctor_id' => 18,
                'vendor_id' => NULL,
                'appointment_id' => 1,
                'encounter_template_id' => NULL,
                'description' => NULL,
                'status' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-05-27 05:57:06',
                'updated_at' => '2024-05-27 05:57:18',
            ),
            2 => 
            array (
                'id' => 3,
                'encounter_date' => $beforeonemonth->format('Y-m-d H:i:s'),
                'user_id' => 10,
                'clinic_id' => 2,
                'doctor_id' => 20,
                'vendor_id' => NULL,
                'appointment_id' => 2,
                'encounter_template_id' => NULL,
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-05-27 05:57:46',
                'updated_at' => '2024-05-27 05:57:46',
            ),
            3 => 
            array (
                'id' => 4,
                'encounter_date' => $beforeonemonth->format('Y-m-d H:i:s'),
                'user_id' => 15,
                'clinic_id' => 4,
                'doctor_id' => 21,
                'vendor_id' => NULL,
                'appointment_id' => 7,
                'encounter_template_id' => NULL,
                'description' => NULL,
                'status' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-05-27 06:38:29',
                'updated_at' => '2024-05-27 06:39:17',
            ),
            4 => 
            array (
                'id' => 5,
                'encounter_date' => $beforeonemonth->format('Y-m-d H:i:s'),
                'user_id' => 16,
                'clinic_id' => 5,
                'doctor_id' => 22,
                'vendor_id' => NULL,
                'appointment_id' => 8,
                'encounter_template_id' => NULL,
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-05-27 06:38:38',
                'updated_at' => '2024-05-27 06:38:38',
            ),
            5 => 
            array (
                'id' => 6,
                'encounter_date' => $beforetwomonth->format('Y-m-d H:i:s'),
                'user_id' => 12,
                'clinic_id' => 3,
                'doctor_id' => 20,
                'vendor_id' => NULL,
                'appointment_id' => 12,
                'encounter_template_id' => NULL,
                'description' => NULL,
                'status' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-05-27 06:38:44',
                'updated_at' => '2024-05-27 06:39:06',
            ),
            6 => 
            array (
                'id' => 7,
                'encounter_date' => $beforeonemonth->format('Y-m-d H:i:s'),
                'user_id' => 12,
                'clinic_id' => 1,
                'doctor_id' => 18,
                'vendor_id' => NULL,
                'appointment_id' => 4,
                'encounter_template_id' => NULL,
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-05-27 06:38:44',
                'updated_at' => '2024-05-27 06:39:06',
            ),
            7 => 
            array (
                'id' => 8,
                'encounter_date' => $beforeonemonth->format('Y-m-d H:i:s'),
                'user_id' => 13,
                'clinic_id' => 3,
                'doctor_id' => 20,
                'vendor_id' => NULL,
                'appointment_id' => 5,
                'encounter_template_id' => NULL,
                'description' => NULL,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-05-27 06:38:44',
                'updated_at' => '2024-05-27 06:39:06',
            ),
        ));
        foreach ($data as $encounter) {
            PatientEncounter::insert($encounter);
        
            // Fetch a single PatientEncounter instance with its related appointmentdetail
            $appointment = PatientEncounter::with('appointmentdetail')->where('id', $encounter['id'])->first();

            $tax_data = optional(optional($appointment->appointmentdetail)->appointmenttransaction)->tax_percentage;
            $service_amount = optional($appointment->appointmentdetail)->service_amount;
            $taxData = $this->calculateTaxAmounts($tax_data ?? null, $service_amount);
            $final_tax_amount = array_sum(array_column($taxData, 'amount'));
            $final_total_amount = optional($appointment->appointmentdetail)->service_amount + $final_tax_amount;
        
            if (optional($appointment->appointmentdetail)->status === "check_in" || optional($appointment->appointmentdetail)->status === "checkout") {
                $billing = [
                    'encounter_id' => $encounter['id'],
                    'user_id' => $encounter['user_id'],
                    'clinic_id' => $encounter['clinic_id'],
                    'doctor_id' => $encounter['doctor_id'],
                    'service_id' => optional($appointment->appointmentdetail)->service_id,
                    'total_amount' => optional($appointment->appointmentdetail)->total_amount,
                    'service_amount' => optional($appointment->appointmentdetail)->service_amount,
                    'discount_amount' => optional(optional($appointment->appointmentdetail)->appointmenttransaction)->discount_amount,
                    'discount_type' => optional(optional($appointment->appointmentdetail)->appointmenttransaction)->discount_type,
                    'discount_value' => optional(optional($appointment->appointmentdetail)->appointmenttransaction)->discount_value,
                    'tax_data' => $tax_data,
                    'date' => $encounter['encounter_date'],
                    'payment_status' => optional(optional($appointment->appointmentdetail)->appointmenttransaction)->payment_status,
                    'final_tax_amount' => $final_tax_amount,
                    'final_total_amount' => $final_total_amount
                ];
                $BillingRecord = BillingRecord::create($billing);
                $BillingRecord = BillingRecord::where('id',$BillingRecord->id)->with('clinicservice')->first();
                if($BillingRecord){
                    $billing_item=[

                        'billing_id' => $BillingRecord->id ?? null,
                        'item_id' => $BillingRecord->service_id ?? null,
                        'item_name' => optional($BillingRecord->clinicservice)->name ?? null,
                        'discount_type' => optional($BillingRecord->clinicservice)->discount_type ?? null,
                        'discount_value' => optional($BillingRecord->clinicservice)->discount_value ?? null,
                        'quantity' => 1,
                        'service_amount' => $BillingRecord->service_amount ?? 0,
                        'total_amount' => $BillingRecord->service_amount  ?? null,
            
                    ];
                    BillingItem::create($billing_item);
                }
            }
        }
        
    }
}
