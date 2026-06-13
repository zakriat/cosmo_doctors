<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Clinic\Transformers\ServiceResource;

class BillingItemResource extends JsonResource
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
        $discount_amount = $this->discount_type == 'percentage' ? $this->service_amount * $this->discount_value / 100 : $this->discount_value;
        return [
            'id' => $this->id,
            'billing_id' => $this->billing_id,
            'item_id' => $this->item_id,
            'item_name' => $this->item_name,
            'discount_value' => $this->discount_value,
            'discount_type' => $this->discount_type,
            'discount_amount' => $this->discount_type == 'percentage' ? ($this->service_amount + $this->inclusive_tax_amount) * $this->discount_value / 100 : $this->discount_value,
            'quantity' => $this->quantity,
            'service_amount' => $this->service_amount,
            'total_amount' => $this->total_amount,
            'service_amount_with_inclusive_tax' => $this->service_amount + $this->inclusive_tax_amount,
            'inclusive_tax_data' => $this->inclusive_tax ? $this->calculateTaxAmounts($this->inclusive_tax, $this->service_amount) : [],
            'total_inclusive_tax' => $this->inclusive_tax_amount,
            'clinic_services' => $this->clinicservice ? 
                new ServiceResource(tap($this->clinicservice, function($clinicservice) {
                    $clinicservice->load(['doctor_service']);
                    $clinicservice->doctor_service->each(function($service) {
                        $service->charges = $this->service_amount;
                        // Modify the price_detail object
                    
                        $service->price_detail = [
                            'service_price' => $this->service_amount,
                            'service_amount' => $this->service_amount,
                            'total_amount' => $this->total_amount,
                            'duration' => $service->clinicservice->duration_min,
                            'discount_type' => $this->discount_type,
                            'discount_value' => $this->discount_value,
                            'discount_amount' => $this->discount_value ? 
                                ($this->discount_type == 'percentage' ? 
                                    ($this->service_amount * $this->discount_value / 100) : 
                                    $this->discount_value) : 0,
                            'service_name' => $service->clinicservice->name ?? '',
                            'inclusive_tax_price' => $service->inclusive_tax_price ?? 0,

                        ];
                    });
                })) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}