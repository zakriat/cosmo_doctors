<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Appointment\Models\BillingRecord;
use Modules\Appointment\Models\BillingItem;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('billing_record', function (Blueprint $table) {
            $table->double('final_discount')->default(0);
            $table->double('final_discount_value')->nullable();
            $table->string('final_discount_type')->nullable();
            $table->double('final_tax_amount')->nullable();
            $table->double('final_total_amount')->nullable();
        });
        $patientencounters = PatientEncounter::with('appointmentdetail')->get();

        
        foreach ($patientencounters as $patientencounter) {
            $tax_data = optional(optional($patientencounter->appointmentdetail)->appointmenttransaction)->tax_percentage;
            if (optional($patientencounter->appointmentdetail)->status === "check_in" || optional($patientencounter->appointmentdetail)->status === "checkout") {
                $billing = [
                    'encounter_id' => $patientencounter['id'],
                    'user_id' => $patientencounter['user_id'],
                    'clinic_id' => $patientencounter['clinic_id'],
                    'doctor_id' => $patientencounter['doctor_id'],
                    'service_id' => optional($patientencounter->appointmentdetail)->service_id,
                    'total_amount' => optional($patientencounter->appointmentdetail)->total_amount,
                    'service_amount' => optional($patientencounter->appointmentdetail)->service_amount,
                    'discount_amount' => optional(optional($patientencounter->appointmentdetail)->appointmenttransaction)->discount_amount,
                    'discount_type' => optional(optional($patientencounter->appointmentdetail)->appointmenttransaction)->discount_type,
                    'discount_value' => optional(optional($patientencounter->appointmentdetail)->appointmenttransaction)->discount_value,
                    'tax_data' => $tax_data,
                    'date' => $patientencounter['encounter_date'],
                    'payment_status' => optional(optional($patientencounter->appointmentdetail)->appointmenttransaction)->payment_status,
                ];
                $BillingRecord = BillingRecord::create($billing);
                $BillingRecord = BillingRecord::where('id',$BillingRecord->id)->with('clinicservice')->first();
                if($BillingRecord){
                    $billing_item=[

                        'billing_id' => $BillingRecord->id ?? null,
                        'item_id' => $BillingRecord->service_id ?? null,
                        'item_name' => optional($BillingRecord->clinicservice)->name ?? null,
                        'discount_type' => optional($BillingRecord->clinicservice)->discount_type  ?? null,
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
