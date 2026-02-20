<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'admission_number' => $this->admission_number,
            'school_class' => new SchoolClassResource($this->whenLoaded('schoolClass')),
            'parent_name' => $this->parent_name,
            'parent_phone' => $this->parent_phone,
            'is_active' => $this->is_active,
        ];
    }
}
