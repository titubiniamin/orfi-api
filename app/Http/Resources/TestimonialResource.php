<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user->first_name . ' ' . $this->user->last_name,
            'type' => ucfirst($this->type),
            'avatar' => $this->user->avatar ?env('AWS_CLOUD_FRONT_URL') .$this->user->avatar : null,
            'message' => $this->message
        ];
    }
}
