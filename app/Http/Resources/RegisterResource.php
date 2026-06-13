<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'api_token' => $this->createToken(setting('app_name'))->plainTextToken,
            'avatar' => $this->avatar,
            'login_type' => $this->login_type,
            'qr_image' => $this->qr_image,
            'user_role' => $this->getRoleNames() ?? [],
            'country_id' => $this->country,
            'state_id' => $this->state,
            'city_id' => $this->city,
            'postal_code' => $this->pincode,
        ];
    }
}
