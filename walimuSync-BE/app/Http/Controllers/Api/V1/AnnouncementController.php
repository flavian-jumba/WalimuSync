<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAnnouncementRequest;
use App\Http\Requests\Api\V1\UpdateAnnouncementRequest;
use App\Http\Resources\Api\V1\AnnouncementResource;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $announcements = Announcement::query()
            ->with(['schoolClass', 'author'])
            ->when($request->query('audience'), fn ($q, $audience) => $q->where('audience', $audience))
            ->when($request->has('is_pinned'), fn ($q) => $q->where('is_pinned', $request->boolean('is_pinned')))
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->paginate();

        return AnnouncementResource::collection($announcements);
    }

    public function show(Announcement $announcement): AnnouncementResource
    {
        $announcement->load(['schoolClass', 'author']);

        return new AnnouncementResource($announcement);
    }

    public function store(StoreAnnouncementRequest $request): JsonResponse
    {
        $announcement = Announcement::create([
            ...$request->validated(),
            'posted_by' => $request->user()->id,
        ]);

        $announcement->load(['schoolClass', 'author']);

        // Notify all teachers with registered devices
        $teachers = User::whereHas('deviceTokens')->get();
        Notification::send($teachers, new NewAnnouncement($announcement));

        return response()->json([
            'message' => 'Announcement created.',
            'data' => new AnnouncementResource($announcement),
        ], 201);
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): JsonResponse
    {
        $announcement->update($request->validated());
        $announcement->load(['schoolClass', 'author']);

        return response()->json([
            'message' => 'Announcement updated.',
            'data' => new AnnouncementResource($announcement),
        ]);
    }

    public function destroy(Announcement $announcement): JsonResponse
    {
        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted.']);
    }

    public function forTeacher(Request $request): AnonymousResourceCollection
    {
        $announcements = Announcement::query()
            ->with(['schoolClass', 'author'])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->where('audience', 'all')
                    ->orWhere('audience', 'teachers');
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->paginate();

        return AnnouncementResource::collection($announcements);
    }
}
