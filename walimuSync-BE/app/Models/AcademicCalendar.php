<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicCalendar extends Model
{
    /** @use HasFactory<\Database\Factories\AcademicCalendarFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'end_date',
        'type',
        'is_all_day',
        'start_time',
        'end_time',
        'description',
        'suppresses_notifications',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'end_date' => 'date',
            'is_all_day' => 'boolean',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'suppresses_notifications' => 'boolean',
        ];
    }

    /**
     * Scope: events that suppress notifications for a given date.
     */
    public function scopeSuppressingOn(Builder $query, Carbon $date): Builder
    {
        return $query
            ->where('suppresses_notifications', true)
            ->where(function (Builder $q) use ($date) {
                // Single-day events
                $q->where(function (Builder $sq) use ($date) {
                    $sq->whereNull('end_date')
                        ->whereDate('date', $date);
                })
                // Multi-day events (date range)
                    ->orWhere(function (Builder $sq) use ($date) {
                        $sq->whereNotNull('end_date')
                            ->whereDate('date', '<=', $date)
                            ->whereDate('end_date', '>=', $date);
                    });
            });
    }

    /**
     * Check if all reminders should be suppressed for the entire day.
     */
    public function suppressesAllDay(): bool
    {
        return $this->suppresses_notifications && $this->is_all_day;
    }

    /**
     * Check if a specific lesson time falls within this event's suppression window.
     */
    public function suppressesAtTime(string $lessonStartTime): bool
    {
        if (! $this->suppresses_notifications) {
            return false;
        }

        if ($this->is_all_day) {
            return true;
        }

        // Partial-day event: check if lesson falls within the meeting window
        if ($this->start_time && $this->end_time) {
            return $lessonStartTime >= Carbon::parse($this->start_time)->format('H:i:s')
                && $lessonStartTime < Carbon::parse($this->end_time)->format('H:i:s');
        }

        return false;
    }

    /**
     * Check if there's a full-day suppression active for a given date.
     */
    public static function isFullDaySuppressed(Carbon $date): bool
    {
        return static::suppressingOn($date)
            ->where('is_all_day', true)
            ->exists();
    }

    /**
     * Get partial-day suppression events for a given date (e.g. meetings).
     */
    public static function getPartialDaySuppressions(Carbon $date): \Illuminate\Database\Eloquent\Collection
    {
        return static::suppressingOn($date)
            ->where('is_all_day', false)
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get();
    }
}
