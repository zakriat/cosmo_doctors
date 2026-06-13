<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceptionistResource extends JsonResource
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
            'receptionist_id' =>optional($this->users)->id,
            'first_name' =>optional($this->users)->first_name,
            'last_name' =>optional($this->users)->last_name,
            'full_name' =>optional($this->users)->full_name,
            'email' => optional($this->users)->email,
            'mobile' =>optional($this->users)->mobile,
            'player_id' =>optional($this->users)->player_id,
            'gender' =>optional($this->users)->gender,
            'date_of_birth' =>optional($this->users)->date_of_birth,
            'email_verified_at' =>optional($this->users)->email_verified_at,
            'status' =>optional($this->users)->status,
            'is_banned' =>optional($this->users)->is_banned,
            'is_manager' =>optional($this->users)->is_manager,
            'country_id' =>optional($this->users)->country,
            'state_id' => optional($this->users)->state,
            'city_id' =>optional($this->users)->city,
            'address' => optional($this->users)->address,
            'pincode' => optional($this->users)->pincode,
            'latitude' => optional($this->users)->latitude,
            'longitude' => optional($this->users)->longitude,
            'clinic_id' => $this->clinic_id,
            'clinic_name' => optional($this->clinics)->name,
            'signature' => $this->Signature,
            'experience' => $this->experience,
            'profile_image' =>optional($this->users)->profile_image,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
