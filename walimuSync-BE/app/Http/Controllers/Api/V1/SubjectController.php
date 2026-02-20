<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SubjectResource;
use App\Models\Subject;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $subjects = Subject::query()->orderBy('name')->paginate();

        return SubjectResource::collection($subjects);
    }

    public function show(Subject $subject): SubjectResource
    {
        return new SubjectResource($subject);
    }
}
