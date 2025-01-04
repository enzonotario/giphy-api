<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \Laravel\Passport\PersonalAccessTokenResult $resource */
        $resource = $this->resource;

        return [
            'token' => $resource->accessToken,
            'expires_at' => $resource->token->expires_at,
        ];
    }
}
