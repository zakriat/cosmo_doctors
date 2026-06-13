<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Transformers\ClinicsResource;

class ClinicServiceMappingResource extends JsonResource
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
            'service_id'=> $this->service_id,
            'clinic_id'=> $this->clinic_id,
            'clinic_data'=> new ClinicsResource($this->center)
            

        ];
    }
}
