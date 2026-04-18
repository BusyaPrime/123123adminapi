<?php

namespace App\Providers;

use App\Domain\Users\Events\NeedVerify;
use App\Domain\Users\Listeners\NeedVerifyListener;
use App\Events\PushNotificationEvent;
use App\Events\SearchStartEvent;
use App\Events\StartOrderListUpdatingEvent;
use App\Listeners\PushNotificationListener;
use App\Listeners\SearchOrderListUpdatingNotification;
use App\Listeners\SearchStartNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Events\ChatMessageSent;
use App\Listeners\ChatMessageSentListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NeedVerify::class => [
            NeedVerifyListener::class,
        ],
        SearchStartEvent::class => [
            SearchStartNotification::class
        ],
        StartOrderListUpdatingEvent::class => [
            SearchOrderListUpdatingNotification::class
        ],
        PushNotificationEvent::class => [
            PushNotificationListener::class
        ],
        ChatMessageSent::class => [
            ChatMessageSentListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
