<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Transformers\ClinicServiceMappingResource;
use Modules\Clinic\Transformers\DoctorServiceMappingResource;
use Modules\Appointment\Trait\AppointmentTrait;



class ServiceResource extends JsonResource
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
        $price = $this->charges;
        $inclusive_tax_data = ['total_inclusive_tax' => 0, 'taxes' => null];

        if(auth()->user() && auth()->user()->hasRole('doctor')){
            $doctor_service =  $this->doctor_service->where('doctor_id',auth()->user()->id);
        }
        else{
            $doctor_service = $this->doctor_service;
        }
        // $inclusive_tax_data = $this->calculate_inclusive_tax($this->charges-$discount_amount,$this->inclusive_tax);

        if ($this->is_inclusive_tax == 1) {
            $inclusive_tax_data = $this->calculate_inclusive_tax($this->charges, $this->inclusive_tax);
            $price = $this->charges + ($inclusive_tax_data['total_inclusive_tax'] ?? 0);
            // dd($price); 
        }

        if($this->discount == 1){
           $discount_amount = ( $this->discount_type == 'percentage')
                                ? $price * $this->discount_value / 100
                                : $this->discount_value;
        }

        // dd($inclusive_tax);
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
            'price'=>$price,
            'category_name' => optional($this->category)->name ?? null,
            'subcategory_name' => optional($this->sub_category)->name ?? null,
            'duration' => $this->duration_min,
            'discount'=> $this->discount,
            'featured'=>optional($this->systemservice)->featured,
            'discount_type'=>$this->discount_type,
            'discount_value'=>$this->discount_value,
            'discount_amount'=>$discount_amount,
            'payable_amount'=> $price - $discount_amount,
            'total_inclusive_tax' => $inclusive_tax_data['total_inclusive_tax'] ?? 0,
            'inclusive_tax_data' => $inclusive_tax_data['taxes'] ?? null,
            'inclusive_tax' => $inclusive_tax_data['taxes'] ?? null,
            'is_enable_advance_payment'=> $this->is_enable_advance_payment,
            'advance_payment_amount' => $this->advance_payment_amount,
            'assign_doctor'=>DoctorServiceMappingResource::collection($doctor_service),
            'time_slot'=>$this->time_slot,
            'is_video_consultancy' => $this->is_video_consultancy,
            'type'=>$this->type,
            'vendor_name'=>optional($this->vendor)->full_name,
            'service_image' => $this->file_url,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
