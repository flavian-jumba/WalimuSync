<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicCalendarResource extends JsonResource
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
            'date' => $this->date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'type' => $this->type,
            'is_all_day' => $this->is_all_day,
            'start_time' => $this->start_time?->format('H:i'),
            'end_time' => $this->end_time?->format('H:i'),
            'description' => $this->description,
            'suppresses_notifications' => $this->suppresses_notifications,
        ];
    }
}
