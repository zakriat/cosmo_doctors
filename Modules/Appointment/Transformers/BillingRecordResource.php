<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BillingRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        
        return [
            'id'=> $this->id,
            'encounter_id'=> $this->encounter_id,
            'user_id'=> $this->user_id,
            'user_name'=>optional($this->user)->first_name .' '.optional($this->user)->last_name,
            'clinic_id'=> $this->clinic_id,  
            'clinic_name'=>optional($this->clinic)->name,
            'doctor_id'=> $this->doctor_id,
            'doctor_name'=>optional($this->doctor)->first_name .' '.optional($this->doctor)->last_name,
            'service_id'=> $this->service_id,
            'service_name'=> optional($this->clinicservice)->name,
            'service_amount'=> $this->service_amount,
            'total_amount'=> $this->total_amount,
            'discount_amount'=> $this->discount_amount,
            'discount_type'=> $this->discount_type,
            'discount_value'=> $this->discount_value,
            'tax_data'=> json_decode($this->tax_data),
            'date'=> $this->date,
            'payment_status'=> $this->payment_status,
            'created_by'=> $this->created_by,
            'updated_by'=> $this->updated_by,
            'deleted_by'=> $this->deleted_by,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'deleted_at'=> $this->deleted_at
        ];
    }
}
