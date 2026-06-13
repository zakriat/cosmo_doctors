<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EncounterDetailsResource extends JsonResource
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
            'encounter_date'=> Carbon::parse($this->encounter_date)->format('Y-m-d'),
            'user_id'=> $this->user_id,
            'user_image' => optional($this->user)->profile_image,
            'user_name'=> optional($this->user)->first_name .' '.optional($this->user)->last_name,
            'user_email'=>optional($this->user)->email,
            'user_mobile'=>optional($this->user)->mobile,
            'country_id' =>optional($this->user)->country,
            'state_id' => optional($this->user)->state,
            'city_id' =>optional($this->user)->city,
            'country_name' => optional(optional($this->user)->countries)->name,
            'state_name' => optional(optional($this->user)->states)->name,
            'city_name' => optional(optional($this->user)->cities)->name,
            'address' => optional($this->user)->address,
            'pincode' => optional($this->user)->pincode,
            'clinic_id'=> $this->clinic_id,  
            'clinic_name'=>optional($this->clinic)->name,
            'doctor_id'=> $this->doctor_id,
            'doctor_name'=>optional($this->doctor)->first_name .' '.optional($this->doctor)->last_name,
            'appointment_id'=> $this->appointment_id,
            'emconter_template_id'=> $this->emconter_template_id,
            'description'=> $this->description,
            'soap' => $this->soap ?? null,
            'status'=> $this->status,
            'created_by'=> $this->created_by,
            'updated_by'=> $this->updated_by,
            'deleted_by'=> $this->deleted_by,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'deleted_at'=> $this->deleted_at
        ];
    }
}
