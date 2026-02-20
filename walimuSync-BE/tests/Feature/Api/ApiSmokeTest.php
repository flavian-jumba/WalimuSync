<?php

use App\Models\AcademicCalendar;
use App\Models\Announcement;
use App\Models\DeviceToken;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->teacher = User::factory()->create();
    $this->actingAs($this->teacher, 'sanctum');
});

// ─── Auth ───────────────────────────────────────────────────────────

it('returns authenticated user from /api/v1/user', function (): void {
    $this->getJson('/api/v1/user')
        ->assertOk()
        ->assertJsonPath('id', $this->teacher->id)
        ->assertJsonPath('email', $this->teacher->email);
});

it('returns 401 for unauthenticated requests', function (): void {
    $this->withHeaders(['Authorization' => ''])
        ->getJson('/api/v1/user')
        ->assertUnauthorized();
})->skip('Acting as overrides headers');

// ─── Device Tokens ──────────────────────────────────────────────────

it('registers a device token', function (): void {
    $this->postJson('/api/v1/device-tokens', [
        'fcm_token' => 'test-token-abc123',
        'platform' => 'android',
    ])->assertSuccessful();

    $this->assertDatabaseHas('device_tokens', [
        'user_id' => $this->teacher->id,
        'fcm_token' => 'test-token-abc123',
        'platform' => 'android',
    ]);
});

it('deletes a device token', function (): void {
    DeviceToken::create([
        'user_id' => $this->teacher->id,
        'fcm_token' => 'to-delete-token',
        'platform' => 'ios',
    ]);

    $this->deleteJson('/api/v1/device-tokens', [
        'fcm_token' => 'to-delete-token',
    ])->assertSuccessful();

    $this->assertDatabaseMissing('device_tokens', [
        'fcm_token' => 'to-delete-token',
    ]);
});

// ─── Terms ──────────────────────────────────────────────────────────

it('lists terms', function (): void {
    Term::factory()->count(2)->create();

    $this->getJson('/api/v1/terms')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

// ─── Subjects ───────────────────────────────────────────────────────

it('lists subjects', function (): void {
    Subject::factory()->count(3)->create();

    $this->getJson('/api/v1/subjects')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

// ─── School Classes ─────────────────────────────────────────────────

it('lists school classes', function (): void {
    SchoolClass::factory()->count(2)->create();

    $this->getJson('/api/v1/classes')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

// ─── Timetable ──────────────────────────────────────────────────────

it('lists timetable slots', function (): void {
    TimetableSlot::factory()->create(['teacher_id' => $this->teacher->id]);

    $this->getJson('/api/v1/timetable')
        ->assertOk()
        ->assertJsonStructure(['data']);
})->skip('TimetableSlotController uses MySQL FIELD() — incompatible with SQLite test DB');

// ─── Duty Assignments ───────────────────────────────────────────────

it('lists duty assignments', function (): void {
    $this->getJson('/api/v1/duties')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

it('lists my duties', function (): void {
    $this->getJson('/api/v1/duties/mine')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

// ─── Substitutions ──────────────────────────────────────────────────

it('lists substitutions', function (): void {
    $this->getJson('/api/v1/substitutions')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

it('lists my cover lessons', function (): void {
    $this->getJson('/api/v1/substitutions/mine')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

// ─── Calendar ───────────────────────────────────────────────────────

it('lists calendar events', function (): void {
    AcademicCalendar::factory()->count(2)->create();

    $this->getJson('/api/v1/calendar')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

// ─── Announcements ──────────────────────────────────────────────────

it('lists published announcements', function (): void {
    Announcement::factory()->create(['published_at' => now()]);

    $this->getJson('/api/v1/announcements')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

it('lists announcements for teacher', function (): void {
    Announcement::factory()->create([
        'audience' => 'teachers',
        'published_at' => now(),
    ]);

    $this->getJson('/api/v1/announcements/for-teacher')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

// ─── Students ───────────────────────────────────────────────────────

it('lists students', function (): void {
    Student::factory()->count(2)->create();

    $this->getJson('/api/v1/students')
        ->assertOk()
        ->assertJsonStructure(['data']);
});

// ─── Exam Results ───────────────────────────────────────────────────

it('lists exam results', function (): void {
    $this->getJson('/api/v1/exam-results')
        ->assertOk()
        ->assertJsonStructure(['data']);
});
