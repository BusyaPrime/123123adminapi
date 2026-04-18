<?php

namespace App\Domain\Users\Notifications;


use App\Domain\Users\Channels\SmsChannel;
use App\Services\SmsService\SmsService;
use Illuminate\Notifications\Notification;

class VerifyCode extends Notification
{

    private $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    /**
     * @param $notifiable
     */
    public function toSms($notifiable)
    {
        $smsService = new SmsService();

        $smsService->send($notifiable->username, trans('sms.verify-code', ['app' => config('app.name')]).': '.$this->code);
    }
}
