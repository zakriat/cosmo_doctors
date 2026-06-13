<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Appointment\Transformers\BillingItemResource;

class BillingRecordDetailsResource extends JsonResource
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
        $discount_type = $this->final_discount_type ?? '';
        $enable_discount = $this->final_discount ?? 0;
        $total_discount = $this->final_discount_value ?? 0;
        $amount_data = $this->billingItem ?? collect();
        if ($discount_type == 'percentage') {
            $billing_discount_total_amount = optional($this->billingItem)->sum('total_amount') * $total_discount / 100;
        } else {
            $billing_discount_total_amount = $total_discount ?? 0;
        }
        $total_amount = $amount_data->sum('total_amount');
        $sub_total = $total_amount - $billing_discount_total_amount;

        $tax_data = $this->calculateTaxAmounts($this->tax_data ?? null, $sub_total);

        $total_tax = array_sum(array_column($tax_data, 'amount'));

        $final_total = $sub_total + $total_tax;

        $advance_payment_status = 0;
        $advance_paid_amount = 0;
        $remaining_payable_amount = 0;
        if (optional(optional(optional($this->patientencounter)->appointmentdetail)->appointmenttransaction)->advance_payment_status == 1) {
            $advance_payment_status = optional(optional(optional($this->patientencounter)->appointmentdetail)->appointmenttransaction)->advance_payment_status;
            $advance_paid_amount = optional(optional($this->patientencounter)->appointmentdetail)->advance_paid_amount;
            $remaining_payable_amount = $final_total - $advance_paid_amount;
        }


        // dd($this);
        $serviceAmount = $total_amount;
        $total_amount = $total_amount ;
        $final_total = $final_total;
        return [
            'id' => $this->id,
            'encounter_id' => $this->encounter_id,
            'user_id' => $this->user_id,
            'user_name' => optional($this->user)->first_name . ' ' . optional($this->user)->last_name,
            'user_address' => optional($this->user)->address,
            'user_gender' => optional($this->user)->gender,
            'user_dob' => optional($this->user)->date_of_birth,
            'clinic_id' => $this->clinic_id,
            'clinic_name' => optional($this->clinic)->name,
            'clinic_email' => optional($this->clinic)->email,
            'clinic_address' => optional($this->clinic)->address,
            'doctor_id' => $this->doctor_id,
            'doctor_name' => optional($this->doctor)->first_name . ' ' . optional($this->doctor)->last_name,
            'doctor_email' => optional($this->doctor)->email,
            'doctor_mobile' => optional($this->doctor)->mobile,
            'service_id' => $this->service_id,
            'service_name' => optional($this->clinicservice)->name,
            'service_amount' => $serviceAmount,
            'total_amount' => $total_amount,
            'discount_amount' => $this->discount_amount,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'is_enable_advance_payment' => optional($this->clinicservice)->is_enable_advance_payment,
            'advance_payment_amount' => optional(optional($this->patientencounter)->appointmentdetail)->advance_payment_amount,
            'advance_payment_status' => $advance_payment_status,
            'advance_paid_amount' => $advance_paid_amount,
            'remaining_payable_amount' => $remaining_payable_amount,
            'billing_final_discount_type' => $discount_type,
            'enable_final_billing_discount' => $enable_discount,
            'billing_final_discount_value' => $total_discount,
            'billing_final_discount_amount' => $billing_discount_total_amount ?? 0,
            'sub_total' => $sub_total,
            'tax' => $tax_data,
            'billing_items' => BillingItemResource::collection($this->billingItem) ?? collect(),
            'total_tax' => $total_tax,
            'final_total' => $final_total,
            'date' => $this->date,
            'payment_status' => $this->payment_status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
