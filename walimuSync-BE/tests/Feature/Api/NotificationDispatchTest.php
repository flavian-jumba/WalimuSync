<?php

use App\Models\DeviceToken;
use App\Models\TimetableSlot;
use App\Models\User;
use App\Notifications\CalendarEventCreated;
use App\Notifications\DutyAssigned;
use App\Notifications\NewAnnouncement;
use App\Notifications\SubstitutionAssigned;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('sends duty assigned notification when creating a duty assignment', function (): void {
    Notification::fake();

    $teacher = User::factory()->create();
    DeviceToken::create([
        'user_id' => $teacher->id,
        'fcm_token' => 'fake-fcm-token-duty',
        'platform' => 'android',
    ]);

    $this->actingAs($teacher, 'sanctum')
        ->postJson('/api/v1/duties', [
            'teacher_id' => $teacher->id,
            'start_date' => '2025-06-01',
            'end_date' => '2025-06-07',
        ])
        ->assertCreated()
        ->assertJsonPath('message', 'Duty assignment created.');

    Notification::assertSentTo($teacher, DutyAssigned::class);
});

it('sends substitution assigned notification when creating a cover lesson', function (): void {
    Notification::fake();

    $authUser = User::factory()->create();
    $substituteTeacher = User::factory()->create();
    DeviceToken::create([
        'user_id' => $substituteTeacher->id,
        'fcm_token' => 'fake-fcm-token-sub',
        'platform' => 'ios',
    ]);

    $slot = TimetableSlot::factory()->create();

    $this->actingAs($authUser, 'sanctum')
        ->postJson('/api/v1/substitutions', [
            'timetable_slot_id' => $slot->id,
            'substitute_teacher_id' => $substituteTeacher->id,
            'date' => '2025-07-10',
        ])
        ->assertCreated()
        ->assertJsonPath('message', 'Cover lesson created.');

    Notification::assertSentTo($substituteTeacher, SubstitutionAssigned::class);
});

it('sends new announcement notification to teachers with device tokens', function (): void {
    Notification::fake();

    $poster = User::factory()->create();
    $teacherWithToken = User::factory()->create();
    $teacherWithoutToken = User::factory()->create();

    DeviceToken::create([
        'user_id' => $teacherWithToken->id,
        'fcm_token' => 'fake-fcm-token-announce',
        'platform' => 'android',
    ]);

    $this->actingAs($poster, 'sanctum')
        ->postJson('/api/v1/announcements', [
            'title' => 'Staff Meeting Friday',
            'body' => 'All staff must attend the meeting.',
            'audience' => 'all',
            'published_at' => now()->toISOString(),
        ])
        ->assertCreated()
        ->assertJsonPath('message', 'Announcement created.');

    Notification::assertSentTo($teacherWithToken, NewAnnouncement::class);
    Notification::assertNotSentTo($teacherWithoutToken, NewAnnouncement::class);
});

it('sends calendar event notification for meetings', function (): void {
    Notification::fake();

    $teacher = User::factory()->create();
    DeviceToken::create([
        'user_id' => $teacher->id,
        'fcm_token' => 'fake-fcm-token-cal',
        'platform' => 'android',
    ]);

    $this->actingAs($teacher, 'sanctum')
        ->postJson('/api/v1/calendar', [
            'title' => 'Staff Meeting',
            'date' => '2025-08-20',
            'type' => 'meeting',
            'is_all_day' => true,
            'description' => 'Monthly staff briefing',
        ])
        ->assertCreated()
        ->assertJsonPath('message', 'Calendar event created.');

    Notification::assertSentTo($teacher, CalendarEventCreated::class);
});

it('does not send calendar notification for holidays', function (): void {
    Notification::fake();

    $teacher = User::factory()->create();
    DeviceToken::create([
        'user_id' => $teacher->id,
        'fcm_token' => 'fake-fcm-token-hol',
        'platform' => 'android',
    ]);

    $this->actingAs($teacher, 'sanctum')
        ->postJson('/api/v1/calendar', [
            'title' => 'Madaraka Day',
            'date' => '2025-06-01',
            'type' => 'holiday',
            'is_all_day' => true,
        ])
        ->assertCreated();

    Notification::assertNothingSent();
});
