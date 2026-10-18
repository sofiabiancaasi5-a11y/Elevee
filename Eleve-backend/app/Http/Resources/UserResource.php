<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'addresses' => $this->whenLoaded('addresses', function () {
                return $this->addresses->map(fn($a) => [
                    'id' => $a->id,
                    'recipient_name' => $a->recipient_name,
                    'phone_number' => $a->phone_number,
                    'street_address' => $a->street_address,
                    'apartment' => $a->apartment,
                    'city' => $a->city,
                    'province_state' => $a->province_state,
                    'postal_code' => $a->postal_code,
                    'country' => $a->country,
                    'is_default' => $a->is_default,
                    'formatted_address' => $a->formatted_address,
                ]);
            }),
        ];
    }
}
