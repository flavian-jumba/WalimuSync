<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTeacherAbsenceRequest;
use App\Http\Resources\Api\V1\TeacherAbsenceResource;
use App\Models\TeacherAbsence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeacherAbsenceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $absences = TeacherAbsence::query()
            ->with('teacher')
            ->when($request->query('teacher_id'), fn ($q, $id) => $q->where('teacher_id', $id))
            ->when($request->query('date'), fn ($q, $date) => $q->whereDate('date', $date))
            ->orderByDesc('date')
            ->paginate();

        return TeacherAbsenceResource::collection($absences);
    }

    public function show(TeacherAbsence $teacherAbsence): TeacherAbsenceResource
    {
        $teacherAbsence->load('teacher');

        return new TeacherAbsenceResource($teacherAbsence);
    }

    public function store(StoreTeacherAbsenceRequest $request): JsonResponse
    {
        $absence = TeacherAbsence::create($request->validated());
        $absence->load('teacher');

        return response()->json([
            'message' => 'Absence recorded.',
            'data' => new TeacherAbsenceResource($absence),
        ], 201);
    }

    public function destroy(TeacherAbsence $teacherAbsence): JsonResponse
    {
        $teacherAbsence->delete();

        return response()->json(['message' => 'Absence record deleted.']);
    }
}
