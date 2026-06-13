<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentTransctionResource extends JsonResource
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
            'id'=> $this->id,
            'appointment_id'=> $this->appointment_id,
            'service_amount'=> $this->service_amount,
            'external_transaction_id'=>$this->external_transaction_id,
            'transaction_type'=>$this->transaction_type,
            'payment_status'=>$this->payment_status,
            'created_by'=> $this->created_by,
            'updated_by'=> $this->updated_by,
            'deleted_by'=> $this->deleted_by,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'deleted_at'=> $this->deleted_at
        ];
    }
}
