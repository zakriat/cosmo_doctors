<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Trait\ClinicTrait;
use Carbon\Carbon;


class ClinicDetailsResource extends JsonResource
{

    use ClinicTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $clinic_session=$this->getClinicSession($this->clinicsessions);
        $session = $this->clinicsessions;
        $clinicholiday = $this->clinicholiday;
        $today = Carbon::today();
        $clinic_status = '';

        $total_services = optional($this->clinicservices)->count();
        $total_doctors = optional($this->clinicdoctor)->count();
        $total_gallery_images = optional($this->clinicgallery)->count();
         
        foreach ($session as $sessionItem) {
            $day = ucfirst($sessionItem->day);
            if ($day === $today->englishDayOfWeek) {
                $holiday = $sessionItem->is_holiday;
                if($holiday == 1){
                    $clinic_status = 'close';
                    break;
                }
                else{
                    foreach ($clinicholiday as $holidayItem) {
                        if ($today->toDateString() === $holidayItem->date) {
                            $clinic_status = 'close';
                            break;  
                        }
                    }
                    if ($clinic_status !== 'close') {
                        $clinic_status = 'open';
                    }
                }
            }
        }
       
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'email' => $this->email,
            'description' => $this->description,
            'system_service_category' => $this->system_service_category,
            'specialty' => optional($this->specialty)->name,
            'contact_number' => $this->contact_number,
            'country_id' => $this->country,
            'state_id' => $this->state,
            'city_id' => $this->city,
            'country_name' => optional($this->countries)->name,
            'state_name' => optional($this->states)->name,
            'city_name' => optional($this->cities)->name,
            'address' => $this->address,
            'pincode' => $this->pincode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'time_slot' => $this->time_slot,
            'status' => $this->status,
            'vendor_id'=>$this->vendor_id,
            'clinic_image' => $this->file_url,
            'clinic_session' => $clinic_session,
            'all_clinic_session'=> $session,
            'clinic_status' => $clinic_status,
            'total_services' => $total_services,
            'total_gallery_images' => $total_gallery_images,
            'total_doctors' => $total_doctors,
            'total_appointments' => $this->total_appointments ?? 0,
            'satisfaction_percentage' => $this->satisfaction_percentage ?? 0,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}