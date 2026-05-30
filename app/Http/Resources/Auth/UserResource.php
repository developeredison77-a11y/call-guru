<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'countryCode' => $this->country_code,
            'mobileNumber' => $this->mobile_number,
            'date_of_birth' => $this->date_of_birth?->toISOString(),
            'sex' => $this->sex,
            'language' => $this->language,
        ];
    }
}
