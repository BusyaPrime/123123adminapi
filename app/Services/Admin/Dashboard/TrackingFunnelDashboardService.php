<?php

namespace App\Services\Admin\Dashboard;

use App\Domain\TrackingDailyStat\Models\TrackingDailyStat;
use App\Services\Tracking\TrackingOrderFunnelDefinition;
use Carbon\Carbon;

class TrackingFunnelDashboardService
{
    const RANGE_TODAY = 'today';
    const RANGE_YESTERDAY = 'yesterday';
    const RANGE_7D = '7d';
    const RANGE_30D = '30d';
    const RANGE_90D = '90d';
    const RANGE_CUSTOM = 'custom';

    public function build(array $filters = array())
    {
        $timezone = config('app.timezone', 'Asia/Tashkent');
        $latestStatDate = $this->latestStatDate();
        $rangeType = $this->resolveRangeType(isset($filters['range']) ? $filters['range'] : null);
        $platform = $this->resolvePlatform(isset($filters['platform']) ? $filters['platform'] : null);
        $range = $this->resolveRange($rangeType, $filters, $latestStatDate, $timezone);

        $summaryRows = $this->summaryRows($range['start'], $range['end'], $platform);
        $stepRows = $this->stepRows($range['start'], $range['end'], $platform);
        $weeklyRows = $this->weeklySummary($range['start'], $range['end'], $platform);
        $weeklyStepRows = $this->weeklyStepRows($range['start'], $range['end'], $platform);
        $timeRows = $this->weeklyTime($range['start'], $range['end'], $platform);

        $summary = $this->buildSummary($summaryRows);
        $steps = $this->buildSteps($summary['started'], $stepRows);
        $dropoff = $this->buildDropoff($steps);
        $stuck = $this->buildStuck($summary, $steps);

        return array(
            'funnelDashboard' => array(
                'generated_at' => Carbon::now($timezone)->format('d.m.Y H:i'),
                'has_data' => $summary['started'] > 0,
                'has_aggregates' => $latestStatDate !== null,
                'filters' => array(
                    'range' => $rangeType,
                    'platform' => $platform,
                    'date_from' => $range['start']->format('Y-m-d'),
                    'date_to' => $range['end']->format('Y-m-d'),
                ),
                'options' => array(
                    'ranges' => $this->rangeOptions(),
                    'platforms' => $this->platformOptions(),
                ),
                'range' => array(
                    'label' => $this->formatRange($range['start'], $range['end']),
                    'latest_aggregate_label' => $latestStatDate ? $latestStatDate->format('d.m.Y') : null,
                ),
                'hero' => array(
                    'eyebrow' => 'Статистика по воронке заказа',
                    'title' => 'Воронка оформления заказа',
                    'subtitle' => 'Здесь видно, сколько пользователей начали оформление заказа, сколько дошли до конца и на каком этапе чаще всего останавливаются.',
                    'descriptor' => 'Панель показывает путь пользователя по шагам оформления заказа.',
                ),
                'kpis' => array(
                    array(
                        'label' => 'Начали',
                        'value' => $summary['started'],
                        'type' => 'number',
                        'hint' => 'Количество пользователей, которые начали оформление',
                    ),
                    array(
                        'label' => 'Завершили',
                        'value' => $summary['completed'],
                        'type' => 'number',
                        'hint' => 'Количество пользователей, которые дошли до завершения',
                    ),
                    array(
                        'label' => 'Итоговая конверсия',
                        'value' => $summary['conversion_pct'],
                        'type' => 'percent',
                        'hint' => 'Доля завершивших от всех начавших',
                    ),
                ),
                'summary' => $summary,
                'charts' => array(
                    'weekly_conversion' => $this->buildWeeklyConversionChart($weeklyRows, $weeklyStepRows),
                    'time_to_complete' => $this->buildTimeChart($timeRows, $summary),
                    'dropoff' => array(
                        'labels' => array_column($dropoff['items'], 'label'),
                        'values' => array_column($dropoff['items'], 'value'),
                        'shares' => array_column($dropoff['items'], 'share_pct'),
                    ),
                    'stuck_gauge' => array(
                        'labels' => array('Не завершили', 'Завершили'),
                        'values' => array($stuck['unresolved_total'], max(0, $summary['started'] - $stuck['unresolved_total'])),
                    ),
                ),
                'steps' => $steps,
                'dropoff' => $dropoff,
                'stuck' => $stuck,
            ),
        );
    }

    protected function latestStatDate()
    {
        $latest = TrackingDailyStat::query()
            ->forFunnel(TrackingOrderFunnelDefinition::FUNNEL_TYPE)
            ->summaryRows()
            ->max('stat_date');

        return $latest ? Carbon::parse($latest) : null;
    }

    protected function resolveRangeType($rangeType)
    {
        $available = array_keys($this->rangeOptions());

        return in_array($rangeType, $available, true) ? $rangeType : static::RANGE_30D;
    }

    protected function resolvePlatform($platform)
    {
        $platforms = TrackingOrderFunnelDefinition::availablePlatforms();

        return in_array($platform, $platforms, true) ? $platform : 'all';
    }

    protected function resolveRange($rangeType, array $filters, $latestStatDate, $timezone)
    {
        $aggregateAnchor = $latestStatDate ?: Carbon::now($timezone)->startOfDay();
        $currentAnchor = Carbon::now($timezone)->startOfDay();

        if ($rangeType === static::RANGE_TODAY) {
            return array('start' => $currentAnchor->copy()->startOfDay(), 'end' => $currentAnchor->copy()->endOfDay());
        }

        if ($rangeType === static::RANGE_YESTERDAY) {
            $date = $currentAnchor->copy()->subDay();
            return array('start' => $date->copy()->startOfDay(), 'end' => $date->copy()->endOfDay());
        }

        if ($rangeType === static::RANGE_7D) {
            return array('start' => $aggregateAnchor->copy()->subDays(6)->startOfDay(), 'end' => $aggregateAnchor->copy()->endOfDay());
        }

        if ($rangeType === static::RANGE_90D) {
            return array('start' => $aggregateAnchor->copy()->subDays(89)->startOfDay(), 'end' => $aggregateAnchor->copy()->endOfDay());
        }

        if ($rangeType === static::RANGE_CUSTOM) {
            $from = isset($filters['date_from']) ? Carbon::parse($filters['date_from'], $timezone)->startOfDay() : $aggregateAnchor->copy()->subDays(29)->startOfDay();
            $to = isset($filters['date_to']) ? Carbon::parse($filters['date_to'], $timezone)->endOfDay() : $aggregateAnchor->copy()->endOfDay();

            if ($from->gt($to)) {
                $swap = $from;
                $from = $to->copy()->startOfDay();
                $to = $swap->copy()->endOfDay();
            }

            return array('start' => $from, 'end' => $to);
        }

        return array('start' => $aggregateAnchor->copy()->subDays(29)->startOfDay(), 'end' => $aggregateAnchor->copy()->endOfDay());
    }

    protected function summaryRows(Carbon $start, Carbon $end, $platform)
    {
        $query = TrackingDailyStat::query()
            ->forFunnel(TrackingOrderFunnelDefinition::FUNNEL_TYPE)
            ->summaryRows()
            ->where('user_type', 'all')
            ->whereBetween('stat_date', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->orderBy('stat_date');

        if ($platform !== 'all') {
            $query->where('platform', $platform);
        } else {
            $query->where('platform', 'all');
        }

        return $query->get();
    }

    protected function stepRows(Carbon $start, Carbon $end, $platform)
    {
        $query = TrackingDailyStat::query()
            ->forFunnel(TrackingOrderFunnelDefinition::FUNNEL_TYPE)
            ->where('user_type', 'all')
            ->where('step_name', '!=', TrackingOrderFunnelDefinition::SUMMARY_STEP)
            ->whereBetween('stat_date', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ));

        if ($platform !== 'all') {
            $query->where('platform', $platform);
        } else {
            $query->where('platform', 'all');
        }

        return $query->get();
    }

    protected function weeklySummary(Carbon $start, Carbon $end, $platform)
    {
        $query = TrackingDailyStat::query()
            ->selectRaw(
                'YEARWEEK(stat_date, 3) as week_key,
                MIN(stat_date) as week_start,
                MAX(stat_date) as week_end,
                SUM(started_count) as started_total,
                SUM(completed_count) as completed_total'
            )
            ->forFunnel(TrackingOrderFunnelDefinition::FUNNEL_TYPE)
            ->summaryRows()
            ->where('user_type', 'all')
            ->whereBetween('stat_date', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->groupBy('week_key')
            ->orderBy('week_key');

        if ($platform !== 'all') {
            $query->where('platform', $platform);
        } else {
            $query->where('platform', 'all');
        }

        return $query->get()->map(function ($row) {
            $started = (int) $row->started_total;
            $completed = (int) $row->completed_total;

            return array(
                'week_key' => (string) $row->week_key,
                'label' => Carbon::parse($row->week_start)->format('d.m') . ' - ' . Carbon::parse($row->week_end)->format('d.m'),
                'started' => $started,
                'completed' => $completed,
                'conversion_pct' => $started > 0 ? round(($completed / $started) * 100, 2) : 0,
            );
        })->all();
    }

    protected function weeklyStepRows(Carbon $start, Carbon $end, $platform)
    {
        $query = TrackingDailyStat::query()
            ->selectRaw(
                'YEARWEEK(stat_date, 3) as week_key,
                step_name,
                MAX(step_order) as step_order,
                SUM(started_count) as started_total,
                SUM(reached_count) as reached_total'
            )
            ->forFunnel(TrackingOrderFunnelDefinition::FUNNEL_TYPE)
            ->where('user_type', 'all')
            ->where('step_name', '!=', TrackingOrderFunnelDefinition::SUMMARY_STEP)
            ->whereBetween('stat_date', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->groupBy('week_key', 'step_name')
            ->orderBy('week_key')
            ->orderBy('step_order');

        if ($platform !== 'all') {
            $query->where('platform', $platform);
        } else {
            $query->where('platform', 'all');
        }

        return $query->get()->map(function ($row) {
            $started = (int) $row->started_total;
            $reached = (int) $row->reached_total;

            return array(
                'week_key' => (string) $row->week_key,
                'step_name' => $row->step_name,
                'step_order' => (int) $row->step_order,
                'conversion_pct' => $started > 0 ? round(($reached / $started) * 100, 2) : 0,
            );
        })->all();
    }

    protected function weeklyTime(Carbon $start, Carbon $end, $platform)
    {
        $query = TrackingDailyStat::query()
            ->selectRaw(
                'YEARWEEK(stat_date, 3) as week_key,
                MIN(stat_date) as week_start,
                MAX(stat_date) as week_end,
                SUM(completed_count) as completed_total,
                SUM(avg_completion_minutes * completed_count) as weighted_avg_minutes,
                SUM(median_completion_minutes * completed_count) as weighted_median_minutes'
            )
            ->forFunnel(TrackingOrderFunnelDefinition::FUNNEL_TYPE)
            ->summaryRows()
            ->where('user_type', 'all')
            ->whereBetween('stat_date', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->groupBy('week_key')
            ->orderBy('week_key');

        if ($platform !== 'all') {
            $query->where('platform', $platform);
        } else {
            $query->where('platform', 'all');
        }

        return $query->get()->map(function ($row) {
            $completed = (int) $row->completed_total;

            return array(
                'week_key' => (string) $row->week_key,
                'label' => Carbon::parse($row->week_start)->format('d.m') . ' - ' . Carbon::parse($row->week_end)->format('d.m'),
                'average_minutes' => $completed > 0 ? round(((float) $row->weighted_avg_minutes) / $completed, 2) : 0,
                'median_minutes' => $completed > 0 ? round(((float) $row->weighted_median_minutes) / $completed, 2) : 0,
            );
        })->all();
    }

    protected function buildSummary($summaryRows)
    {
        $started = (int) $summaryRows->sum('started_count');
        $completed = (int) $summaryRows->sum('completed_count');
        $abandoned = (int) $summaryRows->sum('dropped_count');
        $stuck = (int) $summaryRows->sum('stuck_count');
        $weightedAvg = 0;
        $weightedMedian = 0;
        $completedWeight = 0;

        foreach ($summaryRows as $row) {
            $weight = (int) $row->completed_count;
            $completedWeight += $weight;
            $weightedAvg += ((float) $row->avg_completion_minutes) * $weight;
            $weightedMedian += ((float) $row->median_completion_minutes) * $weight;
        }

        return array(
            'started' => $started,
            'completed' => $completed,
            'abandoned' => $abandoned,
            'stuck' => $stuck,
            'unresolved_total' => $abandoned + $stuck,
            'conversion_pct' => $started > 0 ? round(($completed / $started) * 100, 2) : 0,
            'abandoned_pct' => $started > 0 ? round(($abandoned / $started) * 100, 2) : 0,
            'stuck_pct' => $started > 0 ? round(($stuck / $started) * 100, 2) : 0,
            'unresolved_pct' => $started > 0 ? round((($abandoned + $stuck) / $started) * 100, 2) : 0,
            'avg_completion_minutes' => $completedWeight > 0 ? round($weightedAvg / $completedWeight, 2) : 0,
            'median_completion_minutes' => $completedWeight > 0 ? round($weightedMedian / $completedWeight, 2) : 0,
        );
    }

    protected function buildSteps($started, $stepRows)
    {
        $stepMap = TrackingOrderFunnelDefinition::stepMap();
        $grouped = array();

        foreach ($stepRows as $row) {
            $meta = is_array($row->meta_json) ? $row->meta_json : array();

            if (!isset($grouped[$row->step_name])) {
                $grouped[$row->step_name] = array(
                    'name' => $row->step_name,
                    'label' => TrackingOrderFunnelDefinition::stepLabel($row->step_name),
                    'order' => (int) $row->step_order,
                    'description' => isset($meta['description']) ? $meta['description'] : (isset($stepMap[$row->step_name]['description']) ? $stepMap[$row->step_name]['description'] : ''),
                    'reached' => 0,
                    'dropped' => 0,
                    'stuck' => 0,
                );
            }

            $grouped[$row->step_name]['reached'] += (int) $row->reached_count;
            $grouped[$row->step_name]['dropped'] += (int) $row->dropped_count;
            $grouped[$row->step_name]['stuck'] += (int) $row->stuck_count;
        }

        uasort($grouped, function ($left, $right) {
            return $left['order'] <=> $right['order'];
        });

        $items = array_values($grouped);
        $previousReached = $started;

        foreach ($items as $index => $item) {
            $lossCount = max(0, $previousReached - (int) $item['reached']);
            $lossPct = $previousReached > 0 ? round(($lossCount / $previousReached) * 100, 2) : 0;

            $items[$index]['number'] = str_pad((string) $item['order'], 2, '0', STR_PAD_LEFT);
            $items[$index]['reached_pct'] = $started > 0 ? round(($item['reached'] / $started) * 100, 2) : 0;
            $items[$index]['loss_count'] = $lossCount;
            $items[$index]['loss_pct'] = $lossPct;
            $items[$index]['abandoned_pct'] = $previousReached > 0 ? round(($item['dropped'] / $previousReached) * 100, 2) : 0;
            $items[$index]['stuck_pct'] = $started > 0 ? round(($item['stuck'] / $started) * 100, 2) : 0;
            $items[$index]['severity'] = $this->stepSeverity($lossPct);
            $previousReached = (int) $item['reached'];
        }

        return $items;
    }

    protected function buildDropoff(array $steps)
    {
        $items = array();

        foreach ($steps as $step) {
            if ($step['name'] === 'completed') {
                continue;
            }

            $value = (int) $step['dropped'] + (int) $step['stuck'];

            if ($value <= 0) {
                continue;
            }

            $items[] = array(
                'name' => $step['name'],
                'label' => $step['label'],
                'description' => $step['description'],
                'value' => $value,
            );
        }

        usort($items, function ($left, $right) {
            return $right['value'] <=> $left['value'];
        });

        $total = array_sum(array_column($items, 'value'));

        foreach ($items as $index => $item) {
            $items[$index]['share_pct'] = $total > 0 ? round(($item['value'] / $total) * 100, 2) : 0;
        }

        $bottleneck = !empty($items)
            ? array(
                'label' => $items[0]['label'],
                'value' => $items[0]['value'],
                'share_pct' => $items[0]['share_pct'],
                'interpretation' => 'Больше всего пользователей останавливаются на этом шаге. С него стоит начать разбор причин потерь.',
            )
            : null;

        return array(
            'total' => $total,
            'items' => $items,
            'bottleneck' => $bottleneck,
        );
    }

    protected function buildStuck(array $summary, array $steps)
    {
        $items = array();
        $total = 0;

        foreach ($steps as $step) {
            if ((int) $step['stuck'] <= 0 || $step['name'] === 'completed') {
                continue;
            }

            $items[] = array(
                'label' => $step['label'],
                'value' => (int) $step['stuck'],
                'share' => 0,
                'started_share' => $summary['started'] > 0 ? round(((int) $step['stuck'] / $summary['started']) * 100, 2) : 0,
            );

            $total += (int) $step['stuck'];
        }

        usort($items, function ($left, $right) {
            return $right['value'] <=> $left['value'];
        });

        foreach ($items as $index => $item) {
            $items[$index]['share'] = $total > 0 ? round(($item['value'] / $total) * 100, 2) : 0;
        }

        return array(
            'total' => $total,
            'in_progress_pct' => $summary['stuck_pct'],
            'abandoned_total' => $summary['abandoned'],
            'abandoned_pct' => $summary['abandoned_pct'],
            'unresolved_total' => $summary['unresolved_total'],
            'unresolved_pct' => $summary['unresolved_pct'],
            'items' => $items,
        );
    }

    protected function buildWeeklyConversionChart(array $weeklyRows, array $weeklyStepRows)
    {
        $weeks = array();
        $weekOrder = array();

        foreach ($weeklyRows as $row) {
            $weeks[$row['week_key']] = array(
                'label' => $row['label'],
                'started' => $row['started'],
                'completed' => $row['completed'],
            );
            $weekOrder[] = $row['week_key'];
        }

        foreach ($weeklyStepRows as $row) {
            if (!isset($weeks[$row['week_key']])) {
                $weeks[$row['week_key']] = array(
                    'label' => $row['week_key'],
                    'started' => 0,
                    'completed' => 0,
                );
                $weekOrder[] = $row['week_key'];
            }
        }

        $weekOrder = array_values(array_unique($weekOrder));
        $datasets = array();

        foreach (TrackingOrderFunnelDefinition::steps() as $step) {
            $datasets[$step['name']] = array(
                'name' => $step['name'],
                'label' => $step['label'],
                'order' => $step['order'],
                'values' => array_fill(0, count($weekOrder), 0),
            );
        }

        foreach ($weeklyStepRows as $row) {
            $index = array_search($row['week_key'], $weekOrder, true);

            if ($index === false || !isset($datasets[$row['step_name']])) {
                continue;
            }

            $datasets[$row['step_name']]['values'][$index] = $row['conversion_pct'];
        }

        return array(
            'labels' => array_map(function ($weekKey) use ($weeks) {
                return $weeks[$weekKey]['label'];
            }, $weekOrder),
            'started' => array_map(function ($weekKey) use ($weeks) {
                return $weeks[$weekKey]['started'];
            }, $weekOrder),
            'completed' => array_map(function ($weekKey) use ($weeks) {
                return $weeks[$weekKey]['completed'];
            }, $weekOrder),
            'datasets' => array_values($datasets),
        );
    }

    protected function buildTimeChart(array $timeRows, array $summary)
    {
        $reference = $summary['median_completion_minutes'] ?: $summary['avg_completion_minutes'];

        return array(
            'labels' => array_column($timeRows, 'label'),
            'average_minutes' => array_column($timeRows, 'average_minutes'),
            'median_minutes' => array_column($timeRows, 'median_minutes'),
            'reference_minutes' => $reference,
            'reference_label' => $summary['median_completion_minutes'] ? 'Ориентир периода' : 'Среднее за период',
            'reference_series' => array_fill(0, count($timeRows), $reference),
        );
    }

    protected function stepSeverity($lossPct)
    {
        if ($lossPct >= 25) {
            return 'critical';
        }

        if ($lossPct >= 12) {
            return 'warning';
        }

        return 'stable';
    }

    protected function rangeOptions()
    {
        return array(
            static::RANGE_TODAY => 'Сегодня',
            static::RANGE_YESTERDAY => 'Вчера',
            static::RANGE_7D => '7 дней',
            static::RANGE_30D => '30 дней',
            static::RANGE_90D => '90 дней',
            static::RANGE_CUSTOM => 'Свои даты',
        );
    }

    protected function platformOptions()
    {
        return array(
            'all' => 'Все платформы',
            'android' => 'Android',
            'ios' => 'iOS',
            'web' => 'Web',
        );
    }

    protected function formatRange(Carbon $start, Carbon $end)
    {
        return $start->format('d.m.Y') . ' - ' . $end->format('d.m.Y');
    }
}
