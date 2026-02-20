<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeeCollectionResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'amount' => $this->amount,
            'school_class' => new SchoolClassResource($this->whenLoaded('schoolClass')),
            'term' => new TermResource($this->whenLoaded('term')),
            'assigned_teacher' => new UserResource($this->whenLoaded('assignedTeacher')),
            'due_date' => $this->due_date?->toDateString(),
            'status' => $this->status,
            'total_collected' => $this->whenAggregated('payments', 'amount_paid', 'sum'),
            'payments_count' => $this->whenCounted('payments'),
        ];
    }
}
