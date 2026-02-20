<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimetableSlotRequest;
use App\Models\TimetableSlot;
use Illuminate\Http\JsonResponse;

class StoreTimetableSlotController extends Controller
{
    public function store(StoreTimetableSlotRequest $request): JsonResponse
    {
        $slot = TimetableSlot::create($request->validated());

        return response()->json([
            'message' => 'Timetable slot created successfully.',
            'data' => $slot,
        ]);
    }
}
