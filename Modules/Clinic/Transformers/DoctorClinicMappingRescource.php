<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Transformers\ClinicsResource;

class DoctorClinicMappingRescource extends JsonResource
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
            'id' => $this->id,
            'clinic_id'=> $this->clinic_id,
            'doctor_id'=> $this->doctor_id,
            'name'=> optional($this->clinics)->name,

        ];
    }
}
