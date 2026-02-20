<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule lesson reminders 15 minutes before class starts
// This is a placeholder; implement actual logic in a dedicated command
Schedule::call(function (): void {
    // Example: Query upcoming lessons and send notifications
    // $upcomingLessons = \App\Models\TimetableSlot::whereDate('start_time', today())
    //     ->whereBetween('start_time', [now(), now()->addMinutes(15)])
    //     ->with(['teacher', 'subject', 'schoolClass'])
    //     ->get();
    //
    // foreach ($upcomingLessons as $lesson) {
    //     $lesson->teacher->notify(new \App\Notifications\LessonReminder($lesson));
    // }

    info('Lesson reminder scheduler placeholder executed');
})->everyMinute()->name('lesson-reminders')->withoutOverlapping();

