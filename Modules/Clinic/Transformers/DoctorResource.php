<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

use Modules\Clinic\Transformers\DoctorServiceMappingResource;
use Modules\Clinic\Transformers\DoctorClinicMappingRescource;
use Modules\Clinic\Transformers\EmployeeCommissionResource;
use Modules\Appointment\Models\Appointment;
use Modules\Clinic\Models\DoctorRating;
class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $userId = optional($this->user)->id;

        $totalAppointment = $this->total_appointment ?? Appointment::where('doctor_id', $userId)
            ->where('status', 'checkout')
            ->count();

        $averageRating = $this->average_rating ?? round(
            DoctorRating::where('doctor_id', $userId)->avg('rating') ?? 0,
            1
        );

        $totalPatient = $this->total_patient ?? Appointment::where('doctor_id', $userId)
            ->distinct()
            ->count('user_id');
        
        $totalReviews = $this->total_reviews ?? DoctorRating::where('doctor_id', $userId)->count();

        return [
            'id' => $this->id,
            'doctor_id' =>optional($this->user)->id,
            'first_name' =>optional($this->user)->first_name,
            'last_name' =>optional($this->user)->last_name,
            'full_name' =>optional($this->user)->full_name,
            'email' => optional($this->user)->email,
            'mobile' =>optional($this->user)->mobile,
            'player_id' =>optional($this->user)->player_id,
            'gender' =>optional($this->user)->gender,
            'expert' => optional(optional($this->user)->profile)->expert,
            'date_of_birth' =>optional($this->user)->date_of_birth,
            'email_verified_at' =>optional($this->user)->email_verified_at,
            'status' =>optional($this->user)->status,
            'is_banned' =>optional($this->user)->is_banned,
            'is_manager' =>optional($this->user)->is_manager,
            'country_id' =>optional($this->user)->country,
            'state_id' => optional($this->user)->state,
            'city_id' =>optional($this->user)->city,
            'country_name' => optional(optional($this->user)->countries)->name,
            'state_name' => optional(optional($this->user)->states)->name,
            'city_name' => optional(optional($this->user)->cities)->name,
            'address' => optional($this->user)->address,
            'pincode' => optional($this->user)->pincode,
            'latitude' => optional($this->user)->latitude,
            'longitude' => optional($this->user)->longitude,
            'services'=>DoctorServiceMappingResource::collection($this->doctorService),
            'clinics'=>DoctorClinicMappingRescource::collection($this->doctorclinic),
            'commissions'=>EmployeeCommissionResource::collection($this->doctorCommission),
            'about_self' => optional(optional($this->user)->profile)->about_self,
            'facebook_link' => optional(optional($this->user)->profile)->facebook_link,
            'instagram_link' => optional(optional($this->user)->profile)->instagram_link,
            'twitter_link' => optional(optional($this->user)->profile)->twitter_link,
            'dribbble_link' => optional(optional($this->user)->profile)->dribbble_link,
            'description' => $this->description,
            'signature' => $this->Signature,
            'experience' => $this->experience,
            'profile_image' =>optional($this->user)->profile_image,
            'average_rating' => $averageRating,
            'total_appointment' => $totalAppointment,
            'total_patient' => $totalPatient,
            'total_reviews' => $totalReviews,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
