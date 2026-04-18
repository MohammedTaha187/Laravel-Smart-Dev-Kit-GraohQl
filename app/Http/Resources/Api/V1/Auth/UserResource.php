<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'language_preference' => $this->language_preference,
            'social_provider' => $this->social_provider,
            'social_id' => $this->social_id,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
        ];
    }
}
