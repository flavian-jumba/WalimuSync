<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubstitutionResource extends JsonResource
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
            'timetable_slot' => new TimetableSlotResource($this->whenLoaded('timetableSlot')),
            'substitute_teacher' => new UserResource($this->whenLoaded('substituteTeacher')),
            'date' => $this->date?->toDateString(),
        ];
    }
}
