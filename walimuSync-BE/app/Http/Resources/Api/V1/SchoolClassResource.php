<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolClassResource extends JsonResource
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
            'name' => $this->name,
            'stream' => $this->stream,
            'academic_year' => $this->academic_year,
            'class_teacher' => new UserResource($this->whenLoaded('classTeacher')),
            'students_count' => $this->whenCounted('students'),
        ];
    }
}
