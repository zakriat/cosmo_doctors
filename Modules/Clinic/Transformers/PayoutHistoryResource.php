<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\Setting;

class PayoutHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $timezone = Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';

        return [
            'id' => $this->id,
            'doctor_id' => $this->employee_id,
            'doctor_name' => optional($this->employee)->first_name.' '.optional($this->employee)->last_name,
            'total_amount' => $this->total_amount,
            'commission_amount' => $this->commission_amount,
            'payment_date' => Carbon::parse($this->payment_date)->timezone($timezone)->format('Y-m-d H:i:s'),
            'payment_type' => $this->payment_type,
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
