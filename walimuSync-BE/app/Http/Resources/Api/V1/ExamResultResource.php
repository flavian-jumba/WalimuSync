<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResultResource extends JsonResource
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
            'student' => new StudentResource($this->whenLoaded('student')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'term' => new TermResource($this->whenLoaded('term')),
            'exam_type' => $this->exam_type,
            'score' => $this->score,
            'grade' => $this->grade,
            'remarks' => $this->remarks,
            'recorder' => new UserResource($this->whenLoaded('recorder')),
        ];
    }
}
