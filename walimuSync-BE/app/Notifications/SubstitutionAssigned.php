<?php

namespace App\Notifications;

use App\Models\Substitution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class SubstitutionAssigned extends Notification
{
    use Queueable;

    public function __construct(public Substitution $substitution) {}

    public function via($notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $this->substitution->loadMissing('timetableSlot.subject', 'timetableSlot.schoolClass');

        $slot = $this->substitution->timetableSlot;
        $subjectName = $slot?->subject?->name ?? 'a class';
        $className = $slot?->schoolClass?->name ?? '';
        $date = $this->substitution->date->format('M j, Y');

        $body = "You have been assigned to cover {$subjectName}";
        if ($className) {
            $body .= " for {$className}";
        }
        $body .= " on {$date}.";

        return FcmMessage::create()
            ->data([
                'type' => 'substitution_assigned',
                'substitution_id' => (string) $this->substitution->id,
                'timetable_slot_id' => (string) $this->substitution->timetable_slot_id,
                'date' => $date,
            ])
            ->notification(new FcmNotification(
                title: 'Cover Lesson Assigned',
                body: $body
            ));
    }
}
