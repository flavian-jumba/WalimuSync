<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAcademicCalendarRequest;
use App\Http\Requests\Api\V1\UpdateAcademicCalendarRequest;
use App\Http\Resources\Api\V1\AcademicCalendarResource;
use App\Models\AcademicCalendar;
use App\Models\User;
use App\Notifications\CalendarEventCreated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Notification;

class AcademicCalendarController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $events = AcademicCalendar::query()
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->query('from'), fn ($q, $from) => $q->whereDate('date', '>=', $from))
            ->when($request->query('to'), fn ($q, $to) => $q->whereDate('date', '<=', $to))
            ->orderBy('date')
            ->paginate();

        return AcademicCalendarResource::collection($events);
    }

    public function show(AcademicCalendar $academicCalendar): AcademicCalendarResource
    {
        return new AcademicCalendarResource($academicCalendar);
    }

    public function store(StoreAcademicCalendarRequest $request): JsonResponse
    {
        $event = AcademicCalendar::create($request->validated());

        // Notify all teachers with devices about meetings and events
        if (in_array($event->type, ['meeting', 'event'])) {
            $teachers = User::whereHas('deviceTokens')->get();
            Notification::send($teachers, new CalendarEventCreated($event));
        }

        return response()->json([
            'message' => 'Calendar event created.',
            'data' => new AcademicCalendarResource($event),
        ], 201);
    }

    public function update(UpdateAcademicCalendarRequest $request, AcademicCalendar $academicCalendar): JsonResponse
    {
        $academicCalendar->update($request->validated());

        return response()->json([
            'message' => 'Calendar event updated.',
            'data' => new AcademicCalendarResource($academicCalendar),
        ]);
    }

    public function destroy(AcademicCalendar $academicCalendar): JsonResponse
    {
        $academicCalendar->delete();

        return response()->json(['message' => 'Calendar event deleted.']);
    }
}
