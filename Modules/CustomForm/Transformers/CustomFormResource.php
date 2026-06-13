<?php

namespace Modules\CustomForm\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomFormResource extends JsonResource
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
            'module_type' => $this->module_type,
            'formdata' => json_decode($this->formdata),
            'fields' => json_decode($this->fields),
            'show_in' => json_decode($this->show_in),
            'appointment_status' => json_decode($this->appointment_status),
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
