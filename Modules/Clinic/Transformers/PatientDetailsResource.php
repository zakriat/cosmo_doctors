<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $total_appointments = $this->appointment->count();
        
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'player_id' => $this->player_id,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'email_verified_at' => $this->email_verified_at,
            'status' => $this->status,
            'is_banned' => $this->is_banned,
            'about_self' => optional($this->profile)->about_self,
            'address' => $this->address,
            'city' => optional($this->cities)->name,
            'state' => optional($this->states)->name,
            'country' => optional($this->countries)->name,
            'pincode' => $this->pincode,
            'facebook_link' => optional($this->profile)->facebook_link,
            'instagram_link' => optional($this->profile)->instagram_link,
            'twitter_link' => optional($this->profile)->twitter_link,
            'dribbble_link' => optional($this->profile)->dribbble_link,
            'profile_image' => $this->profile_image,
            'total_appointments' => $total_appointments,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
