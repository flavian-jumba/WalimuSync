<?php

namespace App\Console\Commands;

use App\Models\AcademicCalendar;
use App\Models\TimetableSlot;
use App\Notifications\LessonReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendLessonReminders extends Command
{
    protected $signature = 'app:send-lesson-reminders
                            {--minutes=15 : Minutes before class to send reminder}';

    protected $description = 'Send lesson reminders to teachers, respecting calendar event suppressions';

    public function handle(): int
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $dayOfWeek = strtolower($now->format('l')); // monday, tuesday, etc.
        $minutesAhead = (int) $this->option('minutes');

        // 1. Check for full-day suppression (holidays, breaks, closures)
        if (AcademicCalendar::isFullDaySuppressed($now)) {
            $this->info("Full-day suppression active for {$today}. No reminders sent.");

            return self::SUCCESS;
        }

        // 2. Get partial-day suppressions (meetings with time windows)
        $partialSuppressions = AcademicCalendar::getPartialDaySuppressions($now);

        // 3. Find lessons starting in the next N minutes
        $windowStart = $now->format('H:i:s');
        $windowEnd = $now->copy()->addMinutes($minutesAhead)->format('H:i:s');

        $upcomingLessons = TimetableSlot::query()
            ->where('day_of_week', $dayOfWeek)
            ->whereBetween('start_time', [$windowStart, $windowEnd])
            ->with(['teacher', 'subject', 'schoolClass'])
            ->get();

        if ($upcomingLessons->isEmpty()) {
            $this->info('No upcoming lessons in the next '.$minutesAhead.' minutes.');

            return self::SUCCESS;
        }

        $sent = 0;
        $suppressed = 0;

        foreach ($upcomingLessons as $lesson) {
            // Check if this specific lesson time is suppressed by a partial-day event
            $isSuppressed = $partialSuppressions->contains(
                fn (AcademicCalendar $event) => $event->suppressesAtTime($lesson->start_time)
            );

            if ($isSuppressed) {
                $suppressed++;
                $this->line("  Suppressed: {$lesson->subject->name} at {$lesson->start_time} (calendar event)");

                continue;
            }

            // Send the reminder if teacher exists and has device tokens
            if ($lesson->teacher) {
                $lesson->teacher->notify(new LessonReminder($lesson));
                $sent++;
                $this->line("  Sent: {$lesson->teacher->name} → {$lesson->subject->name} at {$lesson->start_time}");
            }
        }

        $this->info("Done. Sent: {$sent}, Suppressed: {$suppressed}");

        return self::SUCCESS;
    }
}
