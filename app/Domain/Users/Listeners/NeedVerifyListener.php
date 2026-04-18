<?php


namespace App\Domain\Users\Listeners;


use App\Domain\Users\Events\NeedVerify;
use Illuminate\Contracts\Queue\ShouldQueue;

class NeedVerifyListener
{
    /**
     * Handle the event.
     *
     * @param  NeedVerify  $needVerify
     */
    public function handle(NeedVerify $needVerify)
    {
        $needVerify->user->sendVerifyCodeNotification($needVerify->code);
    }
}
