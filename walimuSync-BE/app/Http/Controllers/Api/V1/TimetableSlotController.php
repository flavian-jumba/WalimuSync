<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimetableSlotRequest;
use App\Http\Resources\Api\V1\TimetableSlotResource;
use App\Models\TimetableSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TimetableSlotController extends Controller
{
    private const DAY_ORDER_SQL = "CASE day_of_week
        WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3
        WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6
        WHEN 'Sunday' THEN 7 ELSE 8 END";

    public function index(Request $request): AnonymousResourceCollection
    {
        $slots = TimetableSlot::query()
            ->with(['schoolClass', 'subject', 'teacher', 'term'])
            ->when($request->query('term_id'), fn ($q, $termId) => $q->where('term_id', $termId))
            ->when($request->query('teacher_id'), fn ($q, $teacherId) => $q->where('teacher_id', $teacherId))
            ->when($request->query('school_class_id'), fn ($q, $classId) => $q->where('school_class_id', $classId))
            ->when($request->query('day_of_week'), fn ($q, $day) => $q->where('day_of_week', $day))
            ->orderByRaw(self::DAY_ORDER_SQL)
            ->orderBy('start_time')
            ->paginate();

        return TimetableSlotResource::collection($slots);
    }

    public function show(TimetableSlot $timetableSlot): TimetableSlotResource
    {
        $timetableSlot->load(['schoolClass', 'subject', 'teacher', 'term']);

        return new TimetableSlotResource($timetableSlot);
    }

    public function store(StoreTimetableSlotRequest $request): JsonResponse
    {
        $slot = TimetableSlot::create($request->validated());
        $slot->load(['schoolClass', 'subject', 'teacher', 'term']);

        return response()->json([
            'message' => 'Timetable slot created successfully.',
            'data' => new TimetableSlotResource($slot),
        ], 201);
    }

    public function destroy(TimetableSlot $timetableSlot): JsonResponse
    {
        $timetableSlot->delete();

        return response()->json(['message' => 'Timetable slot deleted.']);
    }

    public function myTimetable(Request $request): AnonymousResourceCollection
    {
        $slots = TimetableSlot::query()
            ->with(['schoolClass', 'subject', 'term'])
            ->where('teacher_id', $request->user()->id)
            ->when($request->query('term_id'), fn ($q, $termId) => $q->where('term_id', $termId))
            ->orderByRaw(self::DAY_ORDER_SQL)
            ->orderBy('start_time')
            ->paginate();

        return TimetableSlotResource::collection($slots);
    }
}
