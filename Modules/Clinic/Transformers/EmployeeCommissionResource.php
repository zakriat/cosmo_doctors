<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Transformers\ClinicsResource;

class EmployeeCommissionResource extends JsonResource
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
            'employee_id'=> $this->employee_id,
            'commission_id'=> $this->commission_id,
            'title'=> optional($this->mainCommission)->title,
            'commission_type'=> optional($this->mainCommission)->commission_type,
            'commission_value'=> optional($this->mainCommission)->commission_value,
            'charges'=> $this->charges,
            'name'=> optional($this->clinicservice)->name,

        ];
    }

}