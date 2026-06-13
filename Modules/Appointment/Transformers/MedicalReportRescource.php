<?php

namespace Modules\Appointment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class MedicalReportRescource extends JsonResource
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
            'user_id'=> $this->user_id,
            'encounter_id'=>$this->encounter_id,
            'name'=>$this->name,
            'date'=>$this->date,
            'file_url'=>$this->file_url,
            'created_by'=> $this->created_by,
            'updated_by'=> $this->updated_by,
            'deleted_by'=> $this->deleted_by,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'deleted_at'=> $this->deleted_at
        ];
    }
}
