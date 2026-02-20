<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSubstitutionRequest;
use App\Http\Resources\Api\V1\SubstitutionResource;
use App\Models\Substitution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubstitutionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $substitutions = Substitution::query()
            ->with(['timetableSlot.schoolClass', 'timetableSlot.subject', 'substituteTeacher'])
            ->when($request->query('date'), fn ($q, $date) => $q->whereDate('date', $date))
            ->when($request->query('substitute_teacher_id'), fn ($q, $id) => $q->where('substitute_teacher_id', $id))
            ->orderByDesc('date')
            ->paginate();

        return SubstitutionResource::collection($substitutions);
    }

    public function show(Substitution $substitution): SubstitutionResource
    {
        $substitution->load(['timetableSlot.schoolClass', 'timetableSlot.subject', 'substituteTeacher']);

        return new SubstitutionResource($substitution);
    }

    public function store(StoreSubstitutionRequest $request): JsonResponse
    {
        $substitution = Substitution::create($request->validated());
        $substitution->load(['timetableSlot.schoolClass', 'timetableSlot.subject', 'substituteTeacher']);

        return response()->json([
            'message' => 'Cover lesson created.',
            'data' => new SubstitutionResource($substitution),
        ], 201);
    }

    public function destroy(Substitution $substitution): JsonResponse
    {
        $substitution->delete();

        return response()->json(['message' => 'Cover lesson deleted.']);
    }

    public function myCoverLessons(Request $request): AnonymousResourceCollection
    {
        $substitutions = Substitution::query()
            ->with(['timetableSlot.schoolClass', 'timetableSlot.subject'])
            ->where('substitute_teacher_id', $request->user()->id)
            ->orderByDesc('date')
            ->paginate();

        return SubstitutionResource::collection($substitutions);
    }
}
