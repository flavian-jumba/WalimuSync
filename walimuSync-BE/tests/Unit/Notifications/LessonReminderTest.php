<?php

use App\Models\User;
use App\Notifications\LessonReminder;
use NotificationChannels\Fcm\FcmChannel;

it('uses fcm channel for lesson reminder notification', function (): void {
    $lesson = createMockLesson();
    $notification = new LessonReminder($lesson);

    $channels = $notification->via(new User());

    expect($channels)->toBe([FcmChannel::class]);
});

it('generates a valid fcm message for lesson reminder', function (): void {
    $lesson = createMockLesson();
    $notification = new LessonReminder($lesson);

    $message = $notification->toFcm(new User());

    $payload = $message->toArray();

    expect($payload)
        ->toHaveKey('data')
        ->toHaveKey('notification');

    expect($payload['data'])->toMatchArray([
        'title' => 'Lesson Reminder',
        'lesson_id' => '1',
    ]);

    expect($payload['data']['body'])
        ->toContain('Math')
        ->toContain('Form 1A')
        ->toContain('08:00:00');

    expect($payload['notification'])->toMatchArray([
        'title' => 'Lesson Reminder',
    ]);
});

function createMockLesson(): object
{
    return new class
    {
        public int $id = 1;

        public string $start_time = '08:00:00';

        public object $subject;

        public object $schoolClass;

        public function __construct()
        {
            $this->subject = (object) ['name' => 'Math'];
            $this->schoolClass = (object) ['name' => 'Form 1A'];
        }
    };
}
