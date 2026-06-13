<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorSessionListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $get_opendays= $this->sessionsForDoctorAndClinic($this->doctor_id, $this->clinic_id)
                  ->where('is_holiday', '!=', 1)->pluck('day')->toArray();
   
      
        return [
            'id' => $this->id,
            'doctor_id' => $this->doctor_id,
            'first_name' =>optional($this->users)->first_name,
            'last_name' =>optional($this->users)->last_name,
            'full_name' =>optional($this->users)->full_name,
            'email' => optional($this->users)->email,
            'mobile' =>optional($this->users)->mobile,
            'clinic_id' => $this->clinic_id,
            'clinic_name' =>optional($this->clinics)->name,
            'doctor_session' =>$get_opendays,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}