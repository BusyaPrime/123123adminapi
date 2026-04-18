<?php


namespace App\Domain\Users\Channels;


use Illuminate\Notifications\Notification;

class SmsChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);

        // Send notification to the $notifiable instance...
    }
}
