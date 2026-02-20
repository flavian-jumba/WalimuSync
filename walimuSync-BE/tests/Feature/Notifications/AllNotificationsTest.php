<?php

use App\Models\AcademicCalendar;
use App\Models\Announcement;
use App\Models\DutyAssignment;
use App\Models\Substitution;
use App\Models\User;
use App\Notifications\CalendarEventCreated;
use App\Notifications\DutyAssigned;
use App\Notifications\NewAnnouncement;
use App\Notifications\SubstitutionAssigned;
use NotificationChannels\Fcm\FcmChannel;

// ─── DutyAssigned ───────────────────────────────────────────────────

it('uses fcm channel for duty assigned notification', function (): void {
    $assignment = new DutyAssignment([
        'teacher_id' => 1,
        'start_date' => '2025-06-01',
        'end_date' => '2025-06-07',
    ]);
    $notification = new DutyAssigned($assignment);

    expect($notification->via(new User))->toBe([FcmChannel::class]);
});

it('generates valid fcm message for duty assigned', function (): void {
    $assignment = new DutyAssignment([
        'id' => 1,
        'teacher_id' => 1,
        'start_date' => '2025-06-01',
        'end_date' => '2025-06-07',
    ]);

    $notification = new DutyAssigned($assignment);
    $payload = $notification->toFcm(new User)->toArray();

    expect($payload)
        ->toHaveKey('data')
        ->toHaveKey('notification');

    expect($payload['data'])->toMatchArray([
        'type' => 'duty_assigned',
        'start_date' => 'Jun 1, 2025',
        'end_date' => 'Jun 7, 2025',
    ]);

    expect($payload['notification']['title'])->toBe('Duty Assignment');
    expect($payload['notification']['body'])->toContain('Jun 1, 2025');
});

// ─── SubstitutionAssigned ───────────────────────────────────────────

it('uses fcm channel for substitution assigned notification', function (): void {
    $substitution = new Substitution([
        'timetable_slot_id' => 1,
        'substitute_teacher_id' => 1,
        'date' => '2025-07-10',
    ]);
    $notification = new SubstitutionAssigned($substitution);

    expect($notification->via(new User))->toBe([FcmChannel::class]);
});

it('generates valid fcm message for substitution assigned', function (): void {
    $slot = new \App\Models\TimetableSlot([
        'id' => 1,
        'school_class_id' => 1,
        'subject_id' => 1,
        'teacher_id' => 1,
        'term_id' => 1,
        'day_of_week' => 'monday',
        'start_time' => '08:00:00',
        'end_time' => '09:00:00',
    ]);
    $slot->setRelation('subject', (object) ['name' => 'Science']);
    $slot->setRelation('schoolClass', (object) ['name' => 'Form 2B']);

    $substitution = new Substitution([
        'id' => 1,
        'timetable_slot_id' => 1,
        'substitute_teacher_id' => 2,
        'date' => '2025-07-10',
    ]);
    $substitution->setRelation('timetableSlot', $slot);

    $notification = new SubstitutionAssigned($substitution);
    $payload = $notification->toFcm(new User)->toArray();

    expect($payload['notification']['title'])->toBe('Cover Lesson Assigned');
    expect($payload['notification']['body'])
        ->toContain('Science')
        ->toContain('Form 2B')
        ->toContain('Jul 10, 2025');

    expect($payload['data']['type'])->toBe('substitution_assigned');
});

// ─── NewAnnouncement ────────────────────────────────────────────────

it('uses fcm channel for new announcement notification', function (): void {
    $announcement = new Announcement([
        'title' => 'Test',
        'body' => 'Test body',
    ]);
    $notification = new NewAnnouncement($announcement);

    expect($notification->via(new User))->toBe([FcmChannel::class]);
});

it('generates valid fcm message for new announcement', function (): void {
    $announcement = new Announcement([
        'id' => 1,
        'title' => 'Staff Meeting Tomorrow',
        'body' => 'All teachers must attend the staff meeting at 10 AM.',
        'audience' => 'teachers',
    ]);

    $notification = new NewAnnouncement($announcement);
    $payload = $notification->toFcm(new User)->toArray();

    expect($payload['notification']['title'])->toBe('Staff Meeting Tomorrow');
    expect($payload['notification']['body'])->toContain('staff meeting');
    expect($payload['data']['type'])->toBe('new_announcement');
    expect($payload['data']['audience'])->toBe('teachers');
});

// ─── CalendarEventCreated ───────────────────────────────────────────

it('uses fcm channel for calendar event notification', function (): void {
    $event = new AcademicCalendar([
        'title' => 'Test Event',
        'date' => '2025-08-15',
        'type' => 'event',
    ]);
    $notification = new CalendarEventCreated($event);

    expect($notification->via(new User))->toBe([FcmChannel::class]);
});

it('generates valid fcm message for calendar meeting event', function (): void {
    $event = new AcademicCalendar([
        'id' => 1,
        'title' => 'Parent-Teacher Conference',
        'date' => '2025-08-15',
        'end_date' => null,
        'type' => 'meeting',
        'description' => 'Term 2 progress review',
    ]);

    $notification = new CalendarEventCreated($event);
    $payload = $notification->toFcm(new User)->toArray();

    expect($payload['notification']['title'])->toBe('Calendar: Parent-Teacher Conference');
    expect($payload['notification']['body'])
        ->toContain('Aug 15, 2025')
        ->toContain('Term 2 progress review');

    expect($payload['data']['type'])->toBe('calendar_event');
    expect($payload['data']['event_type'])->toBe('meeting');
});

it('generates multi-day body for calendar events with end date', function (): void {
    $event = new AcademicCalendar([
        'id' => 2,
        'title' => 'Mid-Term Exams',
        'date' => '2025-09-01',
        'end_date' => '2025-09-05',
        'type' => 'exam',
    ]);

    $notification = new CalendarEventCreated($event);
    $payload = $notification->toFcm(new User)->toArray();

    expect($payload['notification']['body'])
        ->toContain('from Sep 1, 2025 to Sep 5, 2025');
});
