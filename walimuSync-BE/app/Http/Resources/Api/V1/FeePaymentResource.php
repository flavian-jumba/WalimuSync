<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeePaymentResource extends JsonResource
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
            'fee_collection' => new FeeCollectionResource($this->whenLoaded('feeCollection')),
            'student' => new StudentResource($this->whenLoaded('student')),
            'amount_paid' => $this->amount_paid,
            'collector' => new UserResource($this->whenLoaded('collector')),
            'payment_date' => $this->payment_date?->toDateString(),
            'receipt_number' => $this->receipt_number,
            'notes' => $this->notes,
        ];
    }
}
