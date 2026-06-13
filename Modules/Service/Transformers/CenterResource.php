<?php

namespace Modules\Service\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CenterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $type = $request->type;
        if($type == 'clinic'){
            $name = $this->center->clinic_name ?? null;
        }
        return [
            'id' => $this->id,
            'name' => $name,
            'description' => $this->center->description ?? null,
            'address' => $this->center->address ?? null,
            'country' => $this->center->country ?? null,
            'state' => $this->center->state ?? null,
            'city' => $this->center->city ?? null,
            'pincode' => $this->center->pincode ?? null,
            'latitude' => $this->center->latitude ?? null,
            'longitude' => $this->center->longitude ?? null,
            'contact_number' => $this->center->contact_number ?? null,
            'vendor_id' => $this->center->vendor_id ?? null,
            'status' => $this->center->status ?? null,
            'service_image' => $this->center->file_url ?? null,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
