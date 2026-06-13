<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Transformers\DoctorServiceMappingResource;
use Modules\Clinic\Transformers\DoctorClinicMappingRescource;
use Modules\Clinic\Transformers\EmployeeCommissionResource;


class DoctorDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $total_services = $this->doctorService->count();
        $total_reviews = $this->doctorReviews->count();
        $average_rating = $this->doctorReviews->avg('rating');
        $doctor_services = $this->doctorService;
        $services = null;

        foreach($doctor_services as $doctor_service){
            $clinic_names = [];
            $clinics = optional($doctor_service->clinicservice)->ClinicServiceMapping;
            foreach($clinics as $clinic){
                $clinic_names[] = optional($clinic->center)->name;
            }

            $appointments_count = 0;
            if ($doctor_service->clinicservice) {
                $appointments_count = $doctor_service->clinicservice->appointmentService->where('doctor_id', $this->user->id)->count();
            }

            if(auth()->user()){
                $services = DoctorServiceMappingResource::collection($this->doctorService);
            }
            else{
                $services[] = [
                    'id' => optional($doctor_service->clinicservice)->id ,
                    'service_name' => optional($doctor_service->clinicservice)->name,
                    'total_appointments' => $appointments_count,
                    'clinic_name' => $clinic_names,
                ];
            }
        }
        
        
        $rawMobile = optional($this->user)->mobile;
        $formattedMobile = $rawMobile;
        if (!empty($rawMobile)) {
            // Insert a space after a leading "+<country_code>" (1-3 digits)
            $formattedMobile = preg_replace('/^\+(\d{1,3})(\d+)/', '+$1 $2', $rawMobile);
        }

        return [
            'id' => $this->id,
            'doctor_id' =>optional($this->user)->id,
            'first_name' =>optional($this->user)->first_name,
            'last_name' =>optional($this->user)->last_name,
            'full_name' =>optional($this->user)->full_name,
            'email' => optional($this->user)->email,
            'mobile' => $formattedMobile,
            'player_id' =>optional($this->user)->player_id,
            'gender' =>optional($this->user)->gender,
            'expert' => optional(optional($this->user)->profile)->expert,
            'date_of_birth' =>optional($this->user)->date_of_birth,
            'email_verified_at' =>optional($this->user)->email_verified_at,
            'status' =>optional($this->user)->status,
            'is_banned' =>optional($this->user)->is_banned,
            'is_manager' =>optional($this->user)->is_manager,
            'about_self' => optional(optional($this->user)->profile)->about_self,
            'facebook_link' => optional(optional($this->user)->profile)->facebook_link,
            'instagram_link' => optional(optional($this->user)->profile)->instagram_link,
            'twitter_link' => optional(optional($this->user)->profile)->twitter_link,
            'dribbble_link' => optional(optional($this->user)->profile)->dribbble_link,
            'description' => $this->description,
            'signature' => $this->Signature,
            'experience' => $this->experience,
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
            // 'services'=>DoctorServiceMappingResource::collection($this->doctorService),
            'clinics'=>DoctorClinicMappingRescource::collection($this->doctorclinic),
            'commissions'=>EmployeeCommissionResource::collection($this->doctorCommission),
            'profile_image' =>optional($this->user)->profile_image,
            'total_services' => $total_services,
            'total_reviews' => $total_reviews,
            'average_rating' => round($average_rating, 2),
            'reviews' => $this->doctorReviews,
            'qualifications' => $this->doctorDocuments,
            'services' => $services,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
