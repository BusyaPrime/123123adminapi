<?php

namespace App\Notifiable;

use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class FirebaseNotifier extends Notification
{
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return
            FcmMessage::create()
                ->setName('name')
                ->setNotification(
                    (new FcmNotification())
                    ->setTitle('Notification title')
                    ->setBody('Notification body')
                )
                ->setTopic('topic')
                ->setCondition('condition')
                ->setData(['a' => 'b']);
    }
}