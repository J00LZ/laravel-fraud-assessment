<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'child_count' => $this->customers()->count(),
            'fraud_count' => $this->customers()->where('valid', false)->count(),
        ];
    }
}
