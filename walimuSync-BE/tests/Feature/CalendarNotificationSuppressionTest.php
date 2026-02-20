<?php

use App\Models\AcademicCalendar;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('detects full-day suppression for holidays', function () {
    $today = Carbon::today();

    AcademicCalendar::factory()->holiday()->create([
        'title' => 'Madaraka Day',
        'date' => $today,
    ]);

    expect(AcademicCalendar::isFullDaySuppressed($today))->toBeTrue();
});

it('detects full-day suppression for school breaks', function () {
    $today = Carbon::today();

    AcademicCalendar::factory()->schoolBreak()->create([
        'title' => 'End of Term Break',
        'date' => $today->copy()->subDay(),
        'end_date' => $today->copy()->addDays(5),
    ]);

    expect(AcademicCalendar::isFullDaySuppressed($today))->toBeTrue();
});

it('does not suppress when no suppressing events exist', function () {
    $today = Carbon::today();

    AcademicCalendar::factory()->create([
        'title' => 'Sports Day',
        'date' => $today,
        'type' => 'event',
        'suppresses_notifications' => false,
    ]);

    expect(AcademicCalendar::isFullDaySuppressed($today))->toBeFalse();
});

it('does not suppress on different dates', function () {
    AcademicCalendar::factory()->holiday()->create([
        'title' => 'Christmas',
        'date' => Carbon::parse('2026-12-25'),
    ]);

    expect(AcademicCalendar::isFullDaySuppressed(Carbon::parse('2026-12-26')))->toBeFalse();
});

it('suppresses lessons during a meeting window', function () {
    $today = Carbon::today();

    $meeting = AcademicCalendar::factory()->meeting()->create([
        'title' => 'Staff Meeting',
        'date' => $today,
        'start_time' => '08:00',
        'end_time' => '10:00',
    ]);

    // Lesson at 09:00 — during meeting → suppressed
    expect($meeting->suppressesAtTime('09:00:00'))->toBeTrue();

    // Lesson at 10:30 — after meeting → not suppressed
    expect($meeting->suppressesAtTime('10:30:00'))->toBeFalse();

    // Lesson at 07:30 — before meeting → not suppressed
    expect($meeting->suppressesAtTime('07:30:00'))->toBeFalse();

    // Lesson at 10:00 — exactly at end time → not suppressed (end exclusive)
    expect($meeting->suppressesAtTime('10:00:00'))->toBeFalse();
});

it('returns partial-day suppressions for a date', function () {
    $today = Carbon::today();

    AcademicCalendar::factory()->meeting()->create([
        'title' => 'Morning Briefing',
        'date' => $today,
        'start_time' => '07:30',
        'end_time' => '08:30',
    ]);

    AcademicCalendar::factory()->meeting()->create([
        'title' => 'Afternoon Meeting',
        'date' => $today,
        'start_time' => '14:00',
        'end_time' => '15:00',
    ]);

    $suppressions = AcademicCalendar::getPartialDaySuppressions($today);

    expect($suppressions)->toHaveCount(2);
});

it('handles all-day events suppressing at all times', function () {
    $today = Carbon::today();

    $holiday = AcademicCalendar::factory()->holiday()->create([
        'title' => 'Public Holiday',
        'date' => $today,
    ]);

    expect($holiday->suppressesAtTime('09:00:00'))->toBeTrue();
    expect($holiday->suppressesAtTime('14:00:00'))->toBeTrue();
    expect($holiday->suppressesAllDay())->toBeTrue();
});

it('does not suppress for non-suppressing events', function () {
    $today = Carbon::today();

    $event = AcademicCalendar::factory()->create([
        'title' => 'Prize Giving',
        'date' => $today,
        'type' => 'event',
        'suppresses_notifications' => false,
    ]);

    expect($event->suppressesAtTime('09:00:00'))->toBeFalse();
    expect($event->suppressesAllDay())->toBeFalse();
});

it('handles multi-day school closures correctly', function () {
    $startDate = Carbon::parse('2026-04-01');
    $endDate = Carbon::parse('2026-04-14');

    AcademicCalendar::factory()->closure()->create([
        'title' => 'April Holiday',
        'date' => $startDate,
        'end_date' => $endDate,
    ]);

    expect(AcademicCalendar::isFullDaySuppressed(Carbon::parse('2026-04-07')))->toBeTrue();
    expect(AcademicCalendar::isFullDaySuppressed($startDate))->toBeTrue();
    expect(AcademicCalendar::isFullDaySuppressed($endDate))->toBeTrue();
    expect(AcademicCalendar::isFullDaySuppressed(Carbon::parse('2026-04-15')))->toBeFalse();
    expect(AcademicCalendar::isFullDaySuppressed(Carbon::parse('2026-03-31')))->toBeFalse();
});
