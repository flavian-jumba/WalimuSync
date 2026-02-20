<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class LessonReminder extends Notification
{
    use Queueable;

    protected $lesson;

    public function __construct($lesson)
    {
        $this->lesson = $lesson;
    }

    public function via($notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return FcmMessage::create()
            ->data([
                'title' => 'Lesson Reminder',
                'body' => "Your {$this->lesson->subject->name} class for {$this->lesson->schoolClass->name} starts at {$this->lesson->start_time}",
                'lesson_id' => (string) $this->lesson->id,
            ])
            ->notification(new FcmNotification(
                title: 'Lesson Reminder',
                body: "Your {$this->lesson->subject->name} class for {$this->lesson->schoolClass->name} starts at {$this->lesson->start_time}"
            ));
    }
}