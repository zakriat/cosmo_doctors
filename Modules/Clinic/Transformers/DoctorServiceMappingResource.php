<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Transformers\ClinicsResource;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Clinic\Models\ClinicsService;

class DoctorServiceMappingResource extends JsonResource
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
        $service = ClinicsService::where('id', $this->service_id)->first();
        $isAppointmentRequest = in_array($request->path(), [
            'api/appointment-detail',
            'api/appointment-list'
        ]);

        if ($isAppointmentRequest && $this->price_detail) {
            $serviceAmount = $this->price_detail['service_amount'] ?? 0;
            $discountAmount = $this->price_detail['discount_amount'] ?? 0;
            $amountAfterDiscount = $serviceAmount - $discountAmount;
            $inclusive_tax = $this->price_detail['inclusive_tax_price'] ?? 0;
            $finalservcieamount = $amountAfterDiscount + $inclusive_tax;
            
            // Calculate tax amount as float
            $taxAmount =  $this->calculateTaxAmounts(null, $finalservcieamount);
            $total_tax = array_sum(array_column($taxAmount, 'amount'));
            $priceDetail = [
                'service_price' => $serviceAmount,
                'service_amount' => $amountAfterDiscount,
                'total_amount' => ($this->price_detail['total_amount']) + $total_tax,
                'duration' => $this->price_detail['duration'] ?? 30,
                'discount_type' => $this->price_detail['discount_type'] ?? null,
                'discount_value' => $this->price_detail['discount_value'] ?? null,
                'discount_amount' => $discountAmount,
                'service_name' => $this->price_detail['service_name'] ?? optional($this->clinicservice)->name,
                'total_inclusive_tax' => $this->price_detail['inclusive_tax_price'] ?? 0,
                'total_exclusive_tax' => $total_tax  ?? 0,
            ];
        } else {
            $priceDetail = $this->getServiceAmount($this->service_id, $this->doctor_id, $this->clinic_id);
        }


        return [
            'id' => $this->id,
            'service_id'=> $this->service_id,
            'clinic_id'=> $this->clinic_id,
            'doctor_id'=> $this->doctor_id,
            'charges'=> $isAppointmentRequest && $this->price_detail ?$this->price_detail['service_amount']:$this->charges,
            'is_enable_advance_payment'=> optional($service)->is_enable_advance_payment,
            'advance_payment_amount' => optional($service)->advance_payment_amount,
            'price_detail' =>  $priceDetail,//$this->getServiceAmount($this->service_id, $this->doctor_id, $this->clinic_id),
            'name'=> optional($this->clinicservice)->name,
            'doctor_name'=>optional(optional($this->doctors)->user)->full_name,
            'clinic_name'=>optional($this->clinic)->name,
            'doctor_profile'=>optional(optional($this->doctors)->user)->profile_image,
            
        ];
    }
}
