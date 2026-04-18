<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\AggregateTrackingFunnelStats::class,
        \App\Console\Commands\BackfillTrackingFunnelStats::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->exec('curl -s https://api.casva.uz/driverReminderNotification >/dev/null 2>&1')->everyMinute()->runInBackground();
        $schedule->exec('curl -s https://api.casva.uz/confirmationOrders >/dev/null 2>&1')->everyMinute()->runInBackground();
        $schedule->command("logs:clear")->weekly();
        $schedule
            ->command('tracking:funnel-aggregate --date=yesterday')
            ->dailyAt('01:10')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
