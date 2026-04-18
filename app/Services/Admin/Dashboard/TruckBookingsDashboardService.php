<?php

namespace App\Services\Admin\Dashboard;

use App\Domain\TruckBookings\Models\TruckBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TruckBookingsDashboardService
{
    const PERIOD_DAY = 'day';
    const PERIOD_WEEK = 'week';
    const PERIOD_MONTH = 'month';
    const PERIOD_QUARTER = 'quarter';
    const PERIOD_YEAR = 'year';

    /**
     * @param array $filters
     * @return array
     */
    public function build(array $filters = array())
    {
        $timezone = config('app.timezone', 'Asia/Tashkent');
        $latestDoneAt = $this->latestDoneAt($timezone);
        $period = $this->resolvePeriod(isset($filters['period']) ? $filters['period'] : null);
        $anchor = $this->resolveAnchorDate($filters, $latestDoneAt, $timezone, $period);
        $compare = $this->resolveCompare(isset($filters['compare']) ? $filters['compare'] : null);
        $compareAnchor = $compare
            ? $this->resolveCompareAnchor($filters, $anchor, $timezone, $period)
            : $this->defaultCompareAnchor($anchor, $period);
        $clientGroup = $this->resolveClientGroup(isset($filters['client_group']) ? $filters['client_group'] : null);

        $range = $this->buildRange($anchor, $period, $compareAnchor);
        $currentMetrics = $this->periodMetrics($range['current_start'], $range['current_end']);
        $previousMetrics = $this->periodMetrics($range['previous_start'], $range['previous_end']);

        $ytdCurrent = $this->yearMetrics($anchor);
        $ytdPrevious = $this->yearMetrics($compareAnchor ?: $this->defaultCompareAnchor($anchor, self::PERIOD_YEAR));

        $currentDaily = $this->dailyDoneMap($range['current_start'], $range['current_end']);
        $previousDaily = $this->dailyDoneMap($range['previous_start'], $range['previous_end']);

        $mainCurrentBuckets = $this->buildBuckets(
            $range['current_start'],
            $range['current_end'],
            $range['main_bucket_mode']
        );
        $mainPreviousBuckets = $this->buildBuckets(
            $range['previous_start'],
            $range['previous_end'],
            $range['main_bucket_mode']
        );

        $secondaryCurrentBuckets = $this->buildBuckets(
            $range['current_start'],
            $range['current_end'],
            $range['secondary_bucket_mode']
        );
        $secondaryPreviousBuckets = $this->buildBuckets(
            $range['previous_start'],
            $range['previous_end'],
            $range['secondary_bucket_mode']
        );

        $mainSeries = $this->buildSeries(
            $currentDaily,
            $mainCurrentBuckets,
            $previousDaily,
            $mainPreviousBuckets
        );
        $secondarySeries = $this->buildSeries(
            $currentDaily,
            $secondaryCurrentBuckets,
            $previousDaily,
            $secondaryPreviousBuckets
        );

        return array(
            'dashboard' => array(
                'generated_at' => Carbon::now($timezone)->format('d.m.Y H:i'),
                'filters' => array(
                    'year' => (int) $anchor->year,
                    'month' => (int) $anchor->month,
                    'compare_year' => (int) $compareAnchor->year,
                    'compare_month' => (int) $compareAnchor->month,
                    'period' => $period,
                    'compare' => $compare ? 1 : 0,
                    'client_group' => $clientGroup,
                ),
                'options' => array(
                    'years' => $this->yearOptions($latestDoneAt, $timezone),
                    'months' => $this->monthOptions(),
                    'periods' => $this->periodOptions(),
                    'client_groups' => $this->clientGroupOptions(),
                ),
                'range' => array(
                    'current_label' => $this->formatDateRange($range['current_start'], $range['current_end']),
                    'previous_label' => $this->formatDateRange($range['previous_start'], $range['previous_end']),
                    'anchor_label' => $this->anchorLabel($anchor, $period),
                    'main_title' => $range['main_title'],
                    'secondary_title' => $range['secondary_title'],
                ),
                'has_historical_data' => $latestDoneAt !== null,
                'has_data' => (int) $currentMetrics['done_orders'] > 0,
                'kpis' => $this->buildKpis($currentMetrics, $previousMetrics, $ytdCurrent, $ytdPrevious, (int) $anchor->year),
                'charts' => array(
                    'main' => array(
                        'labels' => $mainSeries['labels'],
                        'current' => $mainSeries['current'],
                        'previous' => $mainSeries['previous'],
                    ),
                    'secondary' => array(
                        'labels' => $secondarySeries['labels'],
                        'current' => $secondarySeries['current'],
                        'previous' => $secondarySeries['previous'],
                    ),
                    'payments' => $this->paymentBreakdown($range['current_start'], $range['current_end']),
                    'origins' => $this->originBreakdown($range['current_start'], $range['current_end']),
                ),
                'routes' => $this->routeBreakdown(
                    $range['current_start'],
                    $range['current_end'],
                    $currentMetrics['gmv']
                ),
                'clients' => $this->clientBreakdown(
                    $range['current_start'],
                    $range['current_end'],
                    $range['previous_start'],
                    $range['previous_end'],
                    $currentMetrics['gmv'],
                    $clientGroup
                ),
                'totals' => array(
                    'current' => $currentMetrics,
                    'previous' => $previousMetrics,
                    'ytd_current' => $ytdCurrent,
                    'ytd_previous' => $ytdPrevious,
                ),
            ),
        );
    }

    /**
     * @param string $timezone
     * @return Carbon|null
     */
    protected function latestDoneAt($timezone)
    {
        $latest = DB::table('truck_bookings')
            ->where('status', TruckBooking::STATUS_DONE)
            ->whereNotNull('created_at')
            ->max('created_at');

        if (!$latest) {
            return null;
        }

        return Carbon::parse($latest, $timezone);
    }

    /**
     * @param array $filters
     * @param Carbon|null $latestDoneAt
     * @param string $timezone
     * @param string $period
     * @return Carbon
     */
    protected function resolveAnchorDate(array $filters, $latestDoneAt, $timezone, $period)
    {
        $fallback = $latestDoneAt ?: Carbon::now($timezone);
        $year = isset($filters['year']) ? (int) $filters['year'] : (int) $fallback->year;

        if ($period === self::PERIOD_YEAR) {
            return Carbon::create($year, 12, 31, 23, 59, 59, $timezone);
        }

        $month = isset($filters['month']) ? (int) $filters['month'] : (int) $fallback->month;

        if ($month < 1 || $month > 12) {
            $month = (int) $fallback->month;
        }

        return Carbon::create($year, $month, 1, 0, 0, 0, $timezone)->endOfMonth();
    }

    /**
     * @param array $filters
     * @param Carbon $anchor
     * @param string $timezone
     * @param string $period
     * @return Carbon
     */
    protected function resolveCompareAnchor(array $filters, Carbon $anchor, $timezone, $period)
    {
        $fallback = $this->defaultCompareAnchor($anchor, $period);
        $year = isset($filters['compare_year']) ? (int) $filters['compare_year'] : (int) $fallback->year;

        if ($period === self::PERIOD_YEAR) {
            return Carbon::create($year, 12, 31, 23, 59, 59, $timezone);
        }

        $month = isset($filters['compare_month']) ? (int) $filters['compare_month'] : (int) $fallback->month;

        if ($month < 1 || $month > 12) {
            $month = (int) $fallback->month;
        }

        return Carbon::create($year, $month, 1, 0, 0, 0, $timezone)->endOfMonth();
    }

    /**
     * @param string|null $period
     * @return string
     */
    protected function resolvePeriod($period)
    {
        $available = array(
            self::PERIOD_MONTH,
            self::PERIOD_YEAR,
        );

        return in_array($period, $available, true) ? $period : self::PERIOD_MONTH;
    }

    /**
     * @param mixed $compare
     * @return bool
     */
    protected function resolveCompare($compare)
    {
        if ($compare === null || $compare === '') {
            return true;
        }

        return (int) $compare === 1;
    }

    /**
     * @param Carbon $anchor
     * @param string $period
     * @return Carbon
     */
    protected function defaultCompareAnchor(Carbon $anchor, $period)
    {
        if ($period === self::PERIOD_DAY) {
            return $anchor->copy()->subDay()->endOfDay();
        }

        if ($period === self::PERIOD_WEEK) {
            return $anchor->copy()->subDays(7)->endOfDay();
        }

        if ($period === self::PERIOD_MONTH) {
            return $anchor->copy()->subMonthNoOverflow()->endOfDay();
        }

        if ($period === self::PERIOD_QUARTER) {
            return $anchor->copy()->subMonthsNoOverflow(3)->endOfDay();
        }

        return $anchor->copy()->subYear()->endOfDay();
    }

    /**
     * @param Carbon $anchor
     * @param string $period
     * @return string
     */
    protected function anchorLabel(Carbon $anchor, $period)
    {
        if ($period === self::PERIOD_DAY) {
            return $this->formatSingleDate($anchor);
        }

        if ($period === self::PERIOD_WEEK) {
            return 'Неделя до ' . $this->formatSingleDate($anchor);
        }

        if ($period === self::PERIOD_YEAR) {
            return $anchor->format('Y');
        }

        return $this->monthLabel($anchor);
    }

    /**
     * @param Carbon $anchor
     * @return array
     */
    protected function yearMetrics(Carbon $anchor)
    {
        return $this->periodMetrics(
            $anchor->copy()->startOfYear()->startOfDay(),
            $anchor->copy()->endOfDay()
        );
    }

    /**
     * @param string|null $clientGroup
     * @return string
     */
    protected function resolveClientGroup($clientGroup)
    {
        return $clientGroup === 'employee' ? 'employee' : 'company';
    }

    /**
     * @return array
     */
    protected function clientGroupOptions()
    {
        return array(
            'company' => 'По компаниям',
            'employee' => 'По сотрудникам',
        );
    }

    /**
     * @param Carbon $anchor
     * @param string $period
     * @param Carbon|null $compareAnchor
     * @return array
     */
    protected function buildRange(Carbon $anchor, $period, Carbon $compareAnchor = null)
    {
        $comparisonAnchor = $compareAnchor ?: $this->defaultCompareAnchor($anchor, $period);

        if ($period === self::PERIOD_DAY) {
            $currentStart = $anchor->copy()->startOfDay();
            $currentEnd = $anchor->copy()->endOfDay();
            $previousStart = $comparisonAnchor->copy()->startOfDay();
            $previousEnd = $comparisonAnchor->copy()->endOfDay();

            return array(
                'current_start' => $currentStart,
                'current_end' => $currentEnd,
                'previous_start' => $previousStart,
                'previous_end' => $previousEnd,
                'main_bucket_mode' => 'day',
                'secondary_bucket_mode' => 'day',
                'main_title' => 'GMV по выбранным датам',
                'secondary_title' => 'GMV по дням',
            );
        }

        if ($period === self::PERIOD_WEEK) {
            $currentEnd = $anchor->copy()->endOfDay();
            $currentStart = $currentEnd->copy()->subDays(6)->startOfDay();
            $previousEnd = $comparisonAnchor->copy()->endOfDay();
            $previousStart = $previousEnd->copy()->subDays(6)->startOfDay();

            return array(
                'current_start' => $currentStart,
                'current_end' => $currentEnd,
                'previous_start' => $previousStart,
                'previous_end' => $previousEnd,
                'main_bucket_mode' => 'day',
                'secondary_bucket_mode' => 'day',
                'main_title' => 'GMV по дням выбранной недели',
                'secondary_title' => 'GMV по дням',
            );
        }

        if ($period === self::PERIOD_MONTH) {
            $currentStart = $anchor->copy()->startOfMonth()->startOfDay();
            $currentEnd = $anchor->copy()->endOfDay();
            $previousStart = $comparisonAnchor->copy()->startOfMonth()->startOfDay();
            $previousEnd = $comparisonAnchor->copy()->endOfDay();

            return array(
                'current_start' => $currentStart,
                'current_end' => $currentEnd,
                'previous_start' => $previousStart,
                'previous_end' => $previousEnd,
                'main_bucket_mode' => 'day',
                'secondary_bucket_mode' => 'week',
                'main_title' => 'GMV по дням выбранного периода',
                'secondary_title' => 'GMV по неделям выбранного периода',
            );
        }

        if ($period === self::PERIOD_QUARTER) {
            $currentStart = $anchor->copy()->firstOfQuarter()->startOfDay();
            $currentEnd = $anchor->copy()->endOfDay();
            $previousStart = $comparisonAnchor->copy()->firstOfQuarter()->startOfDay();
            $previousEnd = $comparisonAnchor->copy()->endOfDay();

            return array(
                'current_start' => $currentStart,
                'current_end' => $currentEnd,
                'previous_start' => $previousStart,
                'previous_end' => $previousEnd,
                'main_bucket_mode' => 'month',
                'secondary_bucket_mode' => 'week',
                'main_title' => 'GMV за выбранный квартал',
                'secondary_title' => 'GMV по неделям внутри квартала',
            );
        }

        $currentStart = $anchor->copy()->startOfYear()->startOfDay();
        $currentEnd = $anchor->copy()->endOfDay();
        $previousStart = $comparisonAnchor->copy()->startOfYear()->startOfDay();
        $previousEnd = $comparisonAnchor->copy()->endOfDay();

        return array(
            'current_start' => $currentStart,
            'current_end' => $currentEnd,
            'previous_start' => $previousStart,
            'previous_end' => $previousEnd,
            'main_bucket_mode' => 'month',
            'secondary_bucket_mode' => 'week',
            'main_title' => 'GMV по месяцам выбранного года',
            'secondary_title' => 'GMV по неделям внутри года',
        );
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function periodMetrics(Carbon $start, Carbon $end)
    {
        $row = DB::table('truck_bookings')
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as done_orders,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as canceled_orders,
                COALESCE(SUM(CASE WHEN status = ? THEN price ELSE 0 END), 0) as gmv',
                array(
                    TruckBooking::STATUS_DONE,
                    TruckBooking::STATUS_CANCELED,
                    TruckBooking::STATUS_DONE,
                )
            )
            ->whereBetween('created_at', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->first();

        $doneOrders = (int) ($row && isset($row->done_orders) ? $row->done_orders : 0);
        $canceledOrders = (int) ($row && isset($row->canceled_orders) ? $row->canceled_orders : 0);
        $gmv = (int) ($row && isset($row->gmv) ? $row->gmv : 0);
        $finalizedOrders = $doneOrders + $canceledOrders;

        return array(
            'gmv' => $gmv,
            'done_orders' => $doneOrders,
            'canceled_orders' => $canceledOrders,
            'total_orders' => $finalizedOrders,
            'average_check' => $doneOrders > 0 ? round($gmv / $doneOrders) : 0,
            'cancellation_rate' => $finalizedOrders > 0 ? round(($canceledOrders / $finalizedOrders) * 100, 2) : 0.0,
        );
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function dailyDoneMap(Carbon $start, Carbon $end)
    {
        $rows = DB::table('truck_bookings')
            ->selectRaw('DATE(created_at) as bucket_date, COUNT(*) as orders_count, COALESCE(SUM(price), 0) as gmv')
            ->where('status', TruckBooking::STATUS_DONE)
            ->whereBetween('created_at', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->groupBy('bucket_date')
            ->orderBy('bucket_date')
            ->get();

        $map = array();

        foreach ($rows as $row) {
            $map[$row->bucket_date] = array(
                'orders' => (int) $row->orders_count,
                'gmv' => (int) $row->gmv,
            );
        }

        return $map;
    }

    /**
     * @param array $currentDaily
     * @param array $currentBuckets
     * @param array $previousDaily
     * @param array $previousBuckets
     * @return array
     */
    protected function buildSeries(array $currentDaily, array $currentBuckets, array $previousDaily, array $previousBuckets)
    {
        $labels = array();
        $current = array();
        $previous = array();
        $count = max(count($currentBuckets), count($previousBuckets));

        for ($index = 0; $index < $count; $index++) {
            $currentBucket = isset($currentBuckets[$index]) ? $currentBuckets[$index] : null;
            $previousBucket = isset($previousBuckets[$index]) ? $previousBuckets[$index] : null;

            $labels[] = $currentBucket ? $currentBucket['label'] : ($previousBucket ? $previousBucket['label'] : '');
            $current[] = $currentBucket ? $this->sumBucket($currentDaily, $currentBucket) : 0;
            $previous[] = $previousBucket ? $this->sumBucket($previousDaily, $previousBucket) : 0;
        }

        return array(
            'labels' => $labels,
            'current' => $current,
            'previous' => $previous,
        );
    }

    /**
     * @param array $dailyMap
     * @param array $bucket
     * @return int
     */
    protected function sumBucket(array $dailyMap, array $bucket)
    {
        $total = 0;
        $cursor = $bucket['start']->copy()->startOfDay();
        $end = $bucket['end']->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $key = $cursor->format('Y-m-d');

            if (isset($dailyMap[$key])) {
                $total += (int) $dailyMap[$key]['gmv'];
            }

            $cursor->addDay();
        }

        return $total;
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param string $mode
     * @return array
     */
    protected function buildBuckets(Carbon $start, Carbon $end, $mode)
    {
        $buckets = array();

        if ($mode === 'day') {
            $cursor = $start->copy()->startOfDay();

            while ($cursor->lte($end)) {
                $buckets[] = array(
                    'start' => $cursor->copy()->startOfDay(),
                    'end' => $cursor->copy()->endOfDay(),
                    'label' => $cursor->format('d') . ' ' . $this->shortMonthName((int) $cursor->month),
                );

                $cursor->addDay();
            }

            return $buckets;
        }

        if ($mode === 'week') {
            $cursor = $start->copy()->startOfDay();

            while ($cursor->lte($end)) {
                $bucketStart = $cursor->copy()->startOfDay();
                $bucketEnd = $cursor->copy()->addDays(6)->endOfDay();

                if ($bucketEnd->gt($end)) {
                    $bucketEnd = $end->copy()->endOfDay();
                }

                $buckets[] = array(
                    'start' => $bucketStart,
                    'end' => $bucketEnd,
                    'label' => $this->formatCompactRange($bucketStart, $bucketEnd),
                );

                $cursor = $bucketEnd->copy()->addDay()->startOfDay();
            }

            return $buckets;
        }

        if ($mode === 'month') {
            $cursor = $start->copy()->startOfMonth();

            while ($cursor->lte($end)) {
                $bucketStart = $cursor->copy()->startOfMonth()->startOfDay();
                $bucketEnd = $cursor->copy()->endOfMonth()->endOfDay();

                if ($bucketStart->lt($start)) {
                    $bucketStart = $start->copy()->startOfDay();
                }

                if ($bucketEnd->gt($end)) {
                    $bucketEnd = $end->copy()->endOfDay();
                }

                $buckets[] = array(
                    'start' => $bucketStart,
                    'end' => $bucketEnd,
                    'label' => $this->shortMonthName((int) $cursor->month),
                );

                $cursor->addMonth()->startOfMonth();
            }

            return $buckets;
        }

        $cursor = $start->copy()->startOfMonth();

        while ($cursor->lte($end)) {
            $bucketStart = $cursor->copy()->startOfMonth()->startOfDay();
            $bucketEnd = $cursor->copy()->addMonths(2)->endOfMonth()->endOfDay();

            if ($bucketStart->lt($start)) {
                $bucketStart = $start->copy()->startOfDay();
            }

            if ($bucketEnd->gt($end)) {
                $bucketEnd = $end->copy()->endOfDay();
            }

            $buckets[] = array(
                'start' => $bucketStart,
                'end' => $bucketEnd,
                'label' => $this->shortMonthName((int) $bucketStart->month) . ' - ' . $this->shortMonthName((int) $bucketEnd->month),
            );

            $cursor->addMonths(3)->startOfMonth();
        }

        return $buckets;
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function paymentBreakdown(Carbon $start, Carbon $end)
    {
        $rows = DB::table('truck_bookings')
            ->selectRaw('COALESCE(payment_type, "") as payment_type, COUNT(*) as orders_count, COALESCE(SUM(price), 0) as gmv')
            ->where('status', TruckBooking::STATUS_DONE)
            ->whereBetween('created_at', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->groupBy('payment_type')
            ->orderByDesc('gmv')
            ->get();

        $labels = array();
        $values = array();
        $summary = array();
        $total = 0;

        foreach ($rows as $row) {
            $gmv = (int) $row->gmv;
            $total += $gmv;

            $labels[] = $this->paymentTypeLabel($row->payment_type);
            $values[] = $gmv;
            $summary[] = array(
                'label' => $this->paymentTypeLabel($row->payment_type),
                'orders' => (int) $row->orders_count,
                'gmv' => $gmv,
            );
        }

        foreach ($summary as $index => $item) {
            $summary[$index]['share'] = $total > 0 ? round(($item['gmv'] / $total) * 100, 2) : 0.0;
        }

        return array(
            'labels' => $labels,
            'values' => $values,
            'summary' => $summary,
        );
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function originBreakdown(Carbon $start, Carbon $end)
    {
        $rows = DB::table('truck_bookings as tb')
            ->leftJoin('regions as region_from', 'region_from.id', '=', 'tb.region_from_id')
            ->selectRaw('COALESCE(region_from.title, ?) as region_name, COUNT(*) as orders_count, COALESCE(SUM(tb.price), 0) as gmv', array('Не указано'))
            ->where('tb.status', TruckBooking::STATUS_DONE)
            ->whereBetween('tb.created_at', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->groupBy('tb.region_from_id', 'region_from.title')
            ->orderByDesc('gmv')
            ->limit(6)
            ->get();

        $labels = array();
        $values = array();
        $summary = array();
        $max = 0;

        foreach ($rows as $row) {
            $gmv = (int) $row->gmv;
            $labels[] = $row->region_name;
            $values[] = $gmv;
            $summary[] = array(
                'label' => $row->region_name,
                'orders' => (int) $row->orders_count,
                'gmv' => $gmv,
            );
            $max = max($max, $gmv);
        }

        foreach ($summary as $index => $item) {
            $summary[$index]['width'] = $max > 0 ? round(($item['gmv'] / $max) * 100, 2) : 0;
        }

        return array(
            'labels' => $labels,
            'values' => $values,
            'summary' => $summary,
        );
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param int $currentGmv
     * @return array
     */
    protected function routeBreakdown(Carbon $start, Carbon $end, $currentGmv)
    {
        $rows = DB::table('truck_bookings as tb')
            ->leftJoin('regions as region_from', 'region_from.id', '=', 'tb.region_from_id')
            ->leftJoin('regions as region_to', 'region_to.id', '=', 'tb.region_to_id')
            ->selectRaw(
                'COALESCE(region_from.title, ?) as from_name,
                COALESCE(region_to.title, ?) as to_name,
                COUNT(*) as orders_count,
                COALESCE(SUM(tb.price), 0) as gmv',
                array('Не указано', 'Не указано')
            )
            ->where('tb.status', TruckBooking::STATUS_DONE)
            ->whereBetween('tb.created_at', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->groupBy('tb.region_from_id', 'tb.region_to_id', 'region_from.title', 'region_to.title')
            ->orderByDesc('gmv')
            ->limit(8)
            ->get();

        $items = array();
        $max = 0;

        foreach ($rows as $row) {
            $gmv = (int) $row->gmv;
            $items[] = array(
                'label' => trim($row->from_name . ' - ' . $row->to_name),
                'orders' => (int) $row->orders_count,
                'gmv' => $gmv,
                'share' => $currentGmv > 0 ? round(($gmv / $currentGmv) * 100, 2) : 0.0,
            );
            $max = max($max, $gmv);
        }

        foreach ($items as $index => $item) {
            $items[$index]['width'] = $max > 0 ? round(($item['gmv'] / $max) * 100, 2) : 0;
        }

        return $items;
    }

    /**
     * @param Carbon $currentStart
     * @param Carbon $currentEnd
     * @param Carbon $previousStart
     * @param Carbon $previousEnd
     * @param int $currentGmv
     * @return array
     */
    protected function clientBreakdown(Carbon $currentStart, Carbon $currentEnd, Carbon $previousStart, Carbon $previousEnd, $currentGmv, $groupMode = 'company')
    {
        $currentRows = $this->clientRows($currentStart, $currentEnd);
        $previousRows = $this->clientRows($previousStart, $previousEnd);

        if ($groupMode === 'company') {
            $currentRows = $this->groupClientRowsByCompany($currentRows);
            $previousRows = $this->groupClientRowsByCompany($previousRows);
        }

        $previousMap = array();
        $items = array();

        foreach ($previousRows as $row) {
            $previousMap[$row['key']] = $row;
        }

        foreach ($currentRows as $row) {
            $previousGmv = isset($previousMap[$row['key']]) ? (int) $previousMap[$row['key']]['gmv'] : 0;

            $items[] = array(
                'name' => $row['name'],
                'company' => $row['company'],
                'orders' => $row['orders'],
                'gmv' => $row['gmv'],
                'previous_gmv' => $previousGmv,
                'average_check' => $row['orders'] > 0 ? round($row['gmv'] / $row['orders']) : 0,
                'share' => $currentGmv > 0 ? round(($row['gmv'] / $currentGmv) * 100, 2) : 0.0,
                'delta' => $this->percentChange($row['gmv'], $previousGmv),
            );
        }

        return array_slice($items, 0, 10);
    }

    /**
     * @param array $rows
     * @return array
     */
    protected function groupClientRowsByCompany(array $rows)
    {
        $grouped = array();

        foreach ($rows as $row) {
            $companyId = isset($row['company_id']) ? (int) $row['company_id'] : 0;
            $userId = isset($row['user_id']) ? (int) $row['user_id'] : 0;
            $companyTitle = trim((string) ($row['company'] ?? ''));
            $normalizedCompanyTitle = preg_replace('/\s+/u', ' ', mb_strtolower($companyTitle));
            $hasCompany = $companyTitle !== '' && $companyTitle !== 'Без компании';
            $key = $hasCompany ? 'company:' . $normalizedCompanyTitle : 'user:' . $userId;

            if (!isset($grouped[$key])) {
                $grouped[$key] = array(
                    'key' => $key,
                    'name' => $row['name'],
                    'company' => $companyTitle !== '' ? $companyTitle : 'Без компании',
                    'orders' => 0,
                    'gmv' => 0,
                    'company_id' => $companyId,
                    'user_ids' => array(),
                    'user_names' => array(),
                );
            }

            $grouped[$key]['orders'] += (int) $row['orders'];
            $grouped[$key]['gmv'] += (int) $row['gmv'];

            if ($userId > 0) {
                $grouped[$key]['user_ids'][$userId] = $userId;
            }

            if (!empty($row['name'])) {
                $grouped[$key]['user_names'][$row['name']] = $row['name'];
            }
        }

        foreach ($grouped as $key => $item) {
            if ($item['company'] !== 'Без компании') {
                $usersCount = count($item['user_ids']);

                if ($usersCount > 1) {
                    $grouped[$key]['name'] = $this->employeeCountLabel($usersCount);
                } else {
                    $grouped[$key]['name'] = !empty($item['user_names'])
                        ? reset($item['user_names'])
                        : '1 сотрудник';
                }
            } else {
                $grouped[$key]['name'] = !empty($item['user_names'])
                    ? reset($item['user_names'])
                    : $item['name'];
            }

            unset($grouped[$key]['user_ids'], $grouped[$key]['user_names']);
        }

        usort($grouped, function ($left, $right) {
            return $right['gmv'] <=> $left['gmv'];
        });

        return array_values($grouped);
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function clientRows(Carbon $start, Carbon $end)
    {
        $rows = DB::table('truck_bookings as tb')
            ->leftJoin('user_profiles as up', 'up.user_id', '=', 'tb.user_id')
            ->leftJoin('companies as booking_company', 'booking_company.id', '=', 'tb.client_company_id')
            ->leftJoin('companies as profile_company', 'profile_company.id', '=', 'up.company_id')
            ->select(
                'tb.user_id',
                'tb.client_company_id',
                'up.company_id as profile_company_id',
                'up.surname',
                'up.name',
                'up.middle_name',
                'booking_company.title as booking_company_title',
                'profile_company.title as profile_company_title'
            )
            ->selectRaw('COUNT(*) as orders_count, COALESCE(SUM(tb.price), 0) as gmv')
            ->where('tb.status', TruckBooking::STATUS_DONE)
            ->whereBetween('tb.created_at', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->groupBy(
                'tb.user_id',
                'tb.client_company_id',
                'up.company_id',
                'up.surname',
                'up.name',
                'up.middle_name',
                'booking_company.title',
                'profile_company.title'
            )
            ->orderByDesc('gmv')
            ->get();

        $items = array();

        foreach ($rows as $row) {
            $nameParts = array_filter(array(
                trim((string) $row->surname),
                trim((string) $row->name),
                trim((string) $row->middle_name),
            ));

            $name = trim(implode(' ', $nameParts));
            $company = $row->booking_company_title ?: $row->profile_company_title;
            $userId = $row->user_id ? (int) $row->user_id : 0;
            $companyId = $row->client_company_id ? (int) $row->client_company_id : ((int) $row->profile_company_id);

            if ($name === '' && $company) {
                $name = $company;
            }

            if ($name === '') {
                $name = 'Клиент #' . $userId;
            }

            $items[] = array(
                'key' => $userId . ':' . $companyId,
                'user_id' => $userId,
                'company_id' => $companyId,
                'name' => $name,
                'company' => $company ?: 'Без компании',
                'orders' => (int) $row->orders_count,
                'gmv' => (int) $row->gmv,
            );
        }

        return $items;
    }

    /**
     * @param array $currentMetrics
     * @param array $previousMetrics
     * @param array $ytdCurrent
     * @param array $ytdPrevious
     * @param int $year
     * @return array
     */
    protected function buildKpis(array $currentMetrics, array $previousMetrics, array $ytdCurrent, array $ytdPrevious, $year)
    {
        return array(
            array(
                'label' => 'GMV за период',
                'value' => $currentMetrics['gmv'],
                'value_type' => 'money',
                'accent' => 'primary',
                'compare_value' => $previousMetrics['gmv'],
                'compare_type' => 'money',
                'delta' => $this->percentChange($currentMetrics['gmv'], $previousMetrics['gmv']),
                'delta_type' => 'percent',
            ),
            array(
                'label' => 'GMV за ' . $year,
                'value' => $ytdCurrent['gmv'],
                'value_type' => 'money',
                'accent' => 'teal',
                'compare_value' => $ytdPrevious['gmv'],
                'compare_type' => 'money',
                'delta' => $this->percentChange($ytdCurrent['gmv'], $ytdPrevious['gmv']),
                'delta_type' => 'percent',
            ),
            array(
                'label' => 'Завершенные заказы',
                'value' => $currentMetrics['done_orders'],
                'value_type' => 'number',
                'accent' => 'purple',
                'compare_value' => $previousMetrics['done_orders'],
                'compare_type' => 'number',
                'delta' => $this->percentChange($currentMetrics['done_orders'], $previousMetrics['done_orders']),
                'delta_type' => 'percent',
            ),
            array(
                'label' => 'Средний чек',
                'value' => $currentMetrics['average_check'],
                'value_type' => 'money',
                'accent' => 'compare',
                'compare_value' => $previousMetrics['average_check'],
                'compare_type' => 'money',
                'delta' => $this->percentChange($currentMetrics['average_check'], $previousMetrics['average_check']),
                'delta_type' => 'percent',
            ),
            array(
                'label' => 'Доля отмен',
                'value' => $currentMetrics['cancellation_rate'],
                'value_type' => 'percent',
                'accent' => 'green',
                'compare_value' => $previousMetrics['cancellation_rate'],
                'compare_type' => 'percent',
                'delta' => round($currentMetrics['cancellation_rate'] - $previousMetrics['cancellation_rate'], 2),
                'delta_type' => 'pp',
            ),
        );
    }

    /**
     * @param float|int $current
     * @param float|int $previous
     * @return float|null
     */
    protected function percentChange($current, $previous)
    {
        if ((float) $previous === 0.0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * @param Carbon|null $latestDoneAt
     * @param string $timezone
     * @return array
     */
    protected function yearOptions($latestDoneAt, $timezone)
    {
        $years = DB::table('truck_bookings')
            ->selectRaw('DISTINCT YEAR(created_at) as booking_year')
            ->where('status', TruckBooking::STATUS_DONE)
            ->whereNotNull('created_at')
            ->orderBy('booking_year')
            ->pluck('booking_year')
            ->map(function ($year) {
                return (int) $year;
            })
            ->filter()
            ->values()
            ->all();

        $years[] = (int) Carbon::now($timezone)->year;

        if ($latestDoneAt) {
            $years[] = (int) $latestDoneAt->year;
        }

        $years = array_values(array_unique(array_map('intval', $years)));
        sort($years);

        return $years;
    }

    /**
     * @return array
     */
    protected function monthOptions()
    {
        $months = array();

        for ($month = 1; $month <= 12; $month++) {
            $months[] = array(
                'value' => $month,
                'label' => $this->monthName($month),
            );
        }

        return $months;
    }

    /**
     * @return array
     */
    protected function periodOptions()
    {
        return array(
            self::PERIOD_MONTH => 'Месяц',
            self::PERIOD_YEAR => 'Год',
        );
    }

    /**
     * @param int $count
     * @return string
     */
    protected function employeeCountLabel($count)
    {
        $count = max(1, (int) $count);
        $mod100 = $count % 100;
        $mod10 = $count % 10;

        if ($mod100 >= 11 && $mod100 <= 14) {
            return $count . ' сотрудников';
        }

        if ($mod10 === 1) {
            return $count . ' сотрудник';
        }

        if ($mod10 >= 2 && $mod10 <= 4) {
            return $count . ' сотрудника';
        }

        return $count . ' сотрудников';
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return string
     */
    protected function formatDateRange(Carbon $start, Carbon $end)
    {
        return $start->format('d') . ' ' . $this->monthNameGenitive((int) $start->month) . ' ' . $start->format('Y')
            . ' - '
            . $end->format('d') . ' ' . $this->monthNameGenitive((int) $end->month) . ' ' . $end->format('Y');
    }

    /**
     * @param Carbon $date
     * @return string
     */
    protected function formatSingleDate(Carbon $date)
    {
        return $date->format('d') . ' ' . $this->monthNameGenitive((int) $date->month) . ' ' . $date->format('Y');
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return string
     */
    protected function formatCompactRange(Carbon $start, Carbon $end)
    {
        if ($start->format('m') === $end->format('m')) {
            return $start->format('d') . '-' . $end->format('d') . ' ' . $this->shortMonthName((int) $end->month);
        }

        return $start->format('d') . ' ' . $this->shortMonthName((int) $start->month)
            . ' - '
            . $end->format('d') . ' ' . $this->shortMonthName((int) $end->month);
    }

    /**
     * @param Carbon $date
     * @return string
     */
    protected function monthLabel(Carbon $date)
    {
        return $this->monthName((int) $date->month) . ' ' . $date->format('Y');
    }

    /**
     * @param int $month
     * @return string
     */
    protected function monthName($month)
    {
        $months = array(
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
        );

        return isset($months[$month]) ? $months[$month] : '';
    }

    /**
     * @param int $month
     * @return string
     */
    protected function shortMonthName($month)
    {
        $months = array(
            1 => 'янв',
            2 => 'фев',
            3 => 'мар',
            4 => 'апр',
            5 => 'май',
            6 => 'июн',
            7 => 'июл',
            8 => 'авг',
            9 => 'сен',
            10 => 'окт',
            11 => 'ноя',
            12 => 'дек',
        );

        return isset($months[$month]) ? $months[$month] : '';
    }

    /**
     * @param int $month
     * @return string
     */
    protected function monthNameGenitive($month)
    {
        $months = array(
            1 => 'января',
            2 => 'февраля',
            3 => 'марта',
            4 => 'апреля',
            5 => 'мая',
            6 => 'июня',
            7 => 'июля',
            8 => 'августа',
            9 => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря',
        );

        return isset($months[$month]) ? $months[$month] : '';
    }

    /**
     * @param string|null $paymentType
     * @return string
     */
    protected function paymentTypeLabel($paymentType)
    {
        $labels = array(
            TruckBooking::PAY_CASH => 'Наличные',
            TruckBooking::PAY_TERMINAL => 'Терминал',
            TruckBooking::PAY_COMPANY => 'Компания',
        );

        if (isset($labels[$paymentType])) {
            return $labels[$paymentType];
        }

        return 'Не указан';
    }
}
