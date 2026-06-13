<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BodyChartResource extends JsonResource
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
            'name'=> $this->name,
            'description'=> $this->description,
            'encounter_id'=> $this->encounter_id,
            'appointment_id'=> $this->appointment_id,
            'patient_id' => $this->patient_id,
            'patient_name'=> optional(optional($this->patient_encounter)->user)->first_name.' '.optional(optional($this->patient_encounter)->user)->last_name,
            'doctor_name'=> optional(optional($this->patient_encounter)->doctor)->first_name .' '.optional(optional($this->patient_encounter)->doctor)->last_name,
            'file_url'=>$this->file_url,
            'created_by'=> $this->created_by,
            'updated_by'=> $this->updated_by,
            'deleted_by'=> $this->deleted_by,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'deleted_at'=> $this->deleted_at
        ];
    }
}
