<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Clinic\Transformers\ClinicServiceMappingResource;

class ServiceDetailsResource extends JsonResource
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
        $discount_amount = 0;

        if(auth()->user() && auth()->user()->hasRole('doctor')){
            $doctor_service =  $this->doctor_service->where('doctor_id',auth()->user()->id);
        }
        else{
            $doctor_service = $this->doctor_service;
        }

        $clinics = [];
        foreach($this->ClinicServiceMapping as $service){
            $clinics[] = $service->center;
        }
        // dd($this->charges-$discount_amount);
        if ($this->is_inclusive_tax == 1) {
            $inclusive_tax_data =  $this->calculate_inclusive_tax($this->charges,$this->inclusive_tax);
        } else {
            $inclusive_tax_data = ['taxes' => [], 'total_inclusive_tax' => 0];
        }
        // dd($inclusive_tax_data);
        $charges = ($inclusive_tax_data['total_inclusive_tax'] ?? 0) + $this->charges ?? $this->charges;
        if($this->discount == 1){
            $discount_amount = ( $this->discount_type == 'percentage')
                                 ? $charges * $this->discount_value / 100
                                 : $this->discount_value;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'system_service_id'=>$this->id,
            'slug' => $this->slug,
            'description' => $this->description,
            'charges' => $this->charges,
            'status' => $this->status,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'vendor_id' => $this->vendor_id,
            'category_name' => optional($this->category)->name ?? null,
            'subcategory_name' => optional($this->sub_category)->name ?? null,
            'duration' => $this->duration_min,
            'discount'=> $this->discount,
            'featured'=>optional($this->systemservice)->featured,
            'discount_type'=>$this->discount_type,
            'discount_value'=>$this->discount_value,
            'discount_amount'=>$discount_amount,
            'payable_amount'=> $charges - $discount_amount,
            'total_inclusive_tax' => $inclusive_tax_data['total_inclusive_tax'] ?? 0,
            'inclusive_tax_data' => $inclusive_tax_data['taxes'] ?? null,
            'is_enable_advance_payment'=> $this->is_enable_advance_payment,
            'advance_payment_amount' => $this->advance_payment_amount,
            'time_slot'=>$this->time_slot,
            'is_video_consultancy' => $this->is_video_consultancy,
            'type'=>$this->type,
            'service_image' => $this->file_url,
            'assign_doctor'=>DoctorServiceMappingResource::collection($doctor_service),
            'clinics' => ClinicsResource::collection($clinics),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
