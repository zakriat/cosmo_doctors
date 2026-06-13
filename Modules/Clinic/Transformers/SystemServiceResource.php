<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class SystemServiceResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'is_featured' => $this->featured,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'vendor_id' => $this->vendor_id,
            'category_name' => optional($this->category)->name ?? null,
            'subcategory_name' => optional($this->sub_category)->name ?? null,
            'system_service_image' => $this->file_url,
            'total_services' => optional($this->clinicservice)->count() ?? 0,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
