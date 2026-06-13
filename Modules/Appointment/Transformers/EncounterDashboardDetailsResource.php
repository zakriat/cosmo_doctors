<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EncounterDashboardDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $problems = $this->medicalHistroy()->where('type','encounter_problem')->get();
        $observations = $this->medicalHistroy()->where('type', 'encounter_observations')->get();
        $notes = $this->medicalHistroy()->where('type', 'encounter_notes')->get();
        $prescriptions = $this->prescriptions()->get();
        $other_details = $this->EncounterOtherDetails()->value('other_details') ?? null;
        $medical_report = $this->medicalReport()->get()->makeHidden('media');

        return [
            'id'=> $this->id,
            'encounter_date'=> Carbon::parse($this->encounter_date)->format('Y-m-d'),
            'user_id'=> $this->user_id,
            'user_image' => optional($this->user)->profile_image,
            'user_name'=> optional($this->user)->first_name .' '.optional($this->user)->last_name,
            'user_email'=>optional($this->user)->email,
            'user_mobile'=>optional($this->user)->mobile,
            'user_address'=>optional($this->user)->address,
            'clinic_id'=> $this->clinic_id,  
            'clinic_name'=>optional($this->clinic)->name,
            'doctor_id'=> $this->doctor_id,
            'doctor_name'=>optional($this->doctor)->first_name .' '.optional($this->doctor)->last_name,
            'description'=> $this->description,
            'problems' => $problems,
            'observations' => $observations,
            'notes' => $notes,
            'prescriptions' => $prescriptions,
            'other_details' => $other_details,
            'medical_report' => $medical_report,
            'appointment_id' => $this->appointment_id,
            'body_charts' => $this->bodyChart ?? null,
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
