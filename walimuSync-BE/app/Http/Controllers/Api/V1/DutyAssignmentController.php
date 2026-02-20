<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreDutyAssignmentRequest;
use App\Http\Requests\Api\V1\UpdateDutyAssignmentRequest;
use App\Http\Resources\Api\V1\DutyAssignmentResource;
use App\Models\DutyAssignment;
use App\Notifications\DutyAssigned;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DutyAssignmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $duties = DutyAssignment::query()
            ->with('teacher')
            ->when($request->query('teacher_id'), fn ($q, $id) => $q->where('teacher_id', $id))
            ->orderByDesc('start_date')
            ->paginate();

        return DutyAssignmentResource::collection($duties);
    }

    public function show(DutyAssignment $dutyAssignment): DutyAssignmentResource
    {
        $dutyAssignment->load('teacher');

        return new DutyAssignmentResource($dutyAssignment);
    }

    public function store(StoreDutyAssignmentRequest $request): JsonResponse
    {
        $duty = DutyAssignment::create($request->validated());
        $duty->load('teacher');

        $duty->teacher->notify(new DutyAssigned($duty));

        return response()->json([
            'message' => 'Duty assignment created.',
            'data' => new DutyAssignmentResource($duty),
        ], 201);
    }

    public function update(UpdateDutyAssignmentRequest $request, DutyAssignment $dutyAssignment): JsonResponse
    {
        $dutyAssignment->update($request->validated());
        $dutyAssignment->load('teacher');

        return response()->json([
            'message' => 'Duty assignment updated.',
            'data' => new DutyAssignmentResource($dutyAssignment),
        ]);
    }

    public function destroy(DutyAssignment $dutyAssignment): JsonResponse
    {
        $dutyAssignment->delete();

        return response()->json(['message' => 'Duty assignment deleted.']);
    }

    public function myDuties(Request $request): AnonymousResourceCollection
    {
        $duties = DutyAssignment::query()
            ->where('teacher_id', $request->user()->id)
            ->orderByDesc('start_date')
            ->paginate();

        return DutyAssignmentResource::collection($duties);
    }
}
