<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Modules\Appointment\Trait\AppointmentTrait;

class EncounterServiceResource extends JsonResource
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
        $encounter_id = $this->id;
        $service_id = optional(optional($this->appointment)->clinicservice)->id;
        $service_data = $this->ServiceDetails($encounter_id, $service_id);

        return [
            'id'=> $this->id,
            'encounter_id'=> $this->id,
            'appointment_id'=> $this->appointment_id,
            'clinic_id'=> $this->clinic_id,
            'service_id'=> $service_id,
            'doctor_id'=> $this->doctor_id,
            'user_id'=> $this->user_id,
            'date'=> $this->encounter_date,
            'payment_status'=> 0,
            'service_details'=> $service_data,
        ];
    }
}
