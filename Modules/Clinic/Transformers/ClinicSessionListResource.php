<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ClinicSessionListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $get_opendays= $this->where('clinic_id', $this->clinic_id)->where('is_holiday', '!=', 1)->pluck('day')->toArray();
        
        return [
            'id' => $this->id,
            'clinic_id' => $this->clinic_id,
            'clinic_name' =>optional($this->clinic)->name,
            'email' => optional($this->clinic)->email,
            'contact_number' =>optional($this->clinic)->contact_number,
            'clinic_session' =>$get_opendays,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}