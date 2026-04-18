<?php

namespace App\Console\Commands;

use App\Services\Tracking\TrackingFunnelAggregationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillTrackingFunnelStats extends Command
{
    protected $signature = 'tracking:funnel-backfill {from?} {to?}';

    protected $description = 'Backfill funnel stats for a date range';

    public function handle(TrackingFunnelAggregationService $service)
    {
        $timezone = config('app.timezone', 'Asia/Tashkent');
        $from = $this->argument('from')
            ? Carbon::parse($this->argument('from'), $timezone)->startOfDay()
            : Carbon::yesterday($timezone)->startOfDay();
        $to = $this->argument('to')
            ? Carbon::parse($this->argument('to'), $timezone)->startOfDay()
            : $from->copy();

        if ($from->gt($to)) {
            $swap = $from;
            $from = $to;
            $to = $swap;
        }

        $totalRows = 0;
        $cursor = $from->copy();

        while ($cursor->lte($to)) {
            $rows = $service->aggregateDate($cursor);
            $totalRows += $rows;
            $this->line($cursor->format('Y-m-d') . ': ' . $rows . ' rows');
            $cursor->addDay();
        }

        $this->info('tracking:funnel-backfill finished, total_rows=' . $totalRows);

        return 0;
    }
}
