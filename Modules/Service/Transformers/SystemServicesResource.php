<?php

namespace Modules\Service\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Models\ClinicServiceMapping;

class SystemServicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $clinicMapping = ClinicServiceMapping::with('center')
        ->where('service_id', $this->id)
        ->get();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'charges' => $this->charges,
            'category_id' => optional($this->category)->name,
            'sub_category_id' => optional($this->sub_category)->name,
            'vendor_id' => optional($this->vendor)->first_name . ' ' . optional($this->vendor)->last_name ?? null,
            'duration_min' => $this->duration_min,
            'is_video_consultancy' => $this->is_video_consultancy,
            'status' => $this->status,
            'clinic' => $clinicMapping->count() === 0 ? [] : ($clinicMapping->count() > 1 ? null : CenterResource::collection($clinicMapping)),
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
