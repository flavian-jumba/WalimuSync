<?php

namespace App\Notifications;

use App\Models\DutyAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class DutyAssigned extends Notification
{
    use Queueable;

    public function __construct(public DutyAssignment $assignment) {}

    public function via($notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $startDate = $this->assignment->start_date->format('M j, Y');
        $endDate = $this->assignment->end_date->format('M j, Y');

        return FcmMessage::create()
            ->data([
                'type' => 'duty_assigned',
                'duty_assignment_id' => (string) $this->assignment->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ])
            ->notification(new FcmNotification(
                title: 'Duty Assignment',
                body: "You have been assigned duty from {$startDate} to {$endDate}."
            ));
    }
}
