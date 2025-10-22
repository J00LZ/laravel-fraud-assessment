<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'customerId' => $this->customerId,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'ipAddress' => $this->ip,
            'phoneNumber' => $this->phoneNumber,
            'iban' => $this->iban,
            'dateOfBirth' => $this->dateOfBirth,
            'valid' => $this->valid != 0,
            'invalidReasons' => $this->isValid(),
            'scanId' => $this->scan_id,
        ];
    }
}
