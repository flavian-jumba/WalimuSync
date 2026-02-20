<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SchoolClassResource;
use App\Http\Resources\Api\V1\StudentResource;
use App\Models\SchoolClass;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SchoolClassController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $classes = SchoolClass::query()
            ->with('classTeacher')
            ->withCount('students')
            ->orderBy('name')
            ->paginate();

        return SchoolClassResource::collection($classes);
    }

    public function show(SchoolClass $schoolClass): SchoolClassResource
    {
        $schoolClass->load('classTeacher')->loadCount('students');

        return new SchoolClassResource($schoolClass);
    }

    public function students(SchoolClass $schoolClass): AnonymousResourceCollection
    {
        $students = $schoolClass->students()
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate();

        return StudentResource::collection($students);
    }
}
