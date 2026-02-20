<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send lesson reminders every minute (checks for lessons starting in 15 minutes)
// Respects calendar events: holidays/breaks suppress all reminders, meetings suppress during their window
Schedule::command('app:send-lesson-reminders --minutes=15')
    ->everyMinute()
    ->name('lesson-reminders')
    ->withoutOverlapping();
