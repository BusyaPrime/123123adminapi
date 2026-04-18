<?php

namespace App\Console\Commands;

use App\Services\Tracking\TrackingFunnelAggregationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AggregateTrackingFunnelStats extends Command
{
    protected $signature = 'tracking:funnel-aggregate {--date=}';

    protected $description = 'Aggregate daily funnel stats into tracking_daily_stats';

    public function handle(TrackingFunnelAggregationService $service)
    {
        $timezone = config('app.timezone', 'Asia/Tashkent');
        $dateOption = $this->option('date');

        if ($dateOption === 'yesterday') {
            $targetDate = Carbon::yesterday($timezone);
        } elseif ($dateOption) {
            $targetDate = Carbon::parse($dateOption, $timezone);
        } else {
            $targetDate = Carbon::yesterday($timezone);
        }

        $rows = $service->aggregateDate($targetDate);

        $this->info('tracking:funnel-aggregate finished for ' . $targetDate->format('Y-m-d') . ', rows=' . $rows);

        return 0;
    }
}
