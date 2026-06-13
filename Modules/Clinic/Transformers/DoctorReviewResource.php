<?php

namespace Modules\Clinic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\Setting;

class DoctorReviewResource extends JsonResource
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
            'doctor_id' => $this->doctor_id,
            'title' => $this->title,
            'review_msg' => $this->review_msg,
            'rating' => $this->rating,
            'user_id' => $this->user_id,
            'service_id' => $this->service_id,
            'service_name' => optional($this->clinic_service)->name,
            'created_at' => Carbon::parse($this->created_at)->timezone($timezone)->format('Y-m-d H:i:s'),
            'username' => optional($this->user)->full_name ?? default_user_name(),
            'profile_image' => optional($this->user)->media->pluck('original_url')->first(),
        ];
    }
}
