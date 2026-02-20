<?php

namespace App\Notifications;

use App\Models\AcademicCalendar;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class CalendarEventCreated extends Notification
{
    use Queueable;

    public function __construct(public AcademicCalendar $event) {}

    public function via($notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $date = $this->event->date->format('M j, Y');
        $title = $this->event->title ?? ucfirst($this->event->type);

        $body = "{$title} on {$date}";
        if ($this->event->end_date && ! $this->event->date->eq($this->event->end_date)) {
            $body = "{$title} from {$date} to {$this->event->end_date->format('M j, Y')}";
        }

        if ($this->event->description) {
            $body .= " — {$this->event->description}";
        }

        return FcmMessage::create()
            ->data([
                'type' => 'calendar_event',
                'calendar_event_id' => (string) $this->event->id,
                'event_type' => $this->event->type,
                'date' => $date,
            ])
            ->notification(new FcmNotification(
                title: "Calendar: {$title}",
                body: str($body)->limit(200)->toString()
            ));
    }
}
