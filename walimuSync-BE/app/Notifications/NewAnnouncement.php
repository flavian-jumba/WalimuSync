<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewAnnouncement extends Notification
{
    use Queueable;

    public function __construct(public Announcement $announcement) {}

    public function via($notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $body = str($this->announcement->body)->limit(150)->toString();

        return FcmMessage::create()
            ->data([
                'type' => 'new_announcement',
                'announcement_id' => (string) $this->announcement->id,
                'title' => $this->announcement->title,
                'audience' => $this->announcement->audience ?? 'all',
            ])
            ->notification(new FcmNotification(
                title: $this->announcement->title,
                body: $body
            ));
    }
}
