<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar ? env('AWS_CLOUD_FRONT_URL') . $this->avatar : null,
            'address' => $this->address,
            'email_verified_at' => $this->email_verified_at,
            'date_of_birth' => $this->date_of_birth,
        ];
    }
}
