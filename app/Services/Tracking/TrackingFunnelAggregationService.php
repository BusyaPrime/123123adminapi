<?php

namespace App\Services\Tracking;

use App\Constants\PlatformType;
use App\Constants\TrackingOrderOutcome;
use App\Constants\UserType;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TrackingFunnelAggregationService
{
    const DEFAULT_TIMEZONE = 'Asia/Tashkent';

    public function aggregateDate(Carbon $date)
    {
        $timezone = config('app.timezone', static::DEFAULT_TIMEZONE);
        $targetDate = $date->copy()->timezone($timezone)->startOfDay();
        $start = $targetDate->copy()->startOfDay();
        $end = $targetDate->copy()->endOfDay();

        $funnels = DB::table('tracking_order_funnels as funnel')
            ->join('tracking_sessions as session', 'session.id', '=', 'funnel.session_id')
            ->leftJoin('truck_bookings as booking', 'booking.id', '=', 'funnel.order_id')
            ->select(
                'funnel.id',
                'funnel.session_id',
                'funnel.user_id',
                'funnel.order_id',
                'funnel.max_step_reached',
                'funnel.steps_data',
                'funnel.outcome',
                'funnel.total_time_seconds',
                'funnel.created_at',
                'funnel.ended_at',
                'session.platform',
                'session.user_type',
                'session.ip_address',
                'session.device_id',
                'session.duration_seconds',
                'booking.price as order_price'
            )
            ->whereBetween('funnel.created_at', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->get()
            ->map(function ($row) {
                $row->steps_data = $this->normalizeSteps($row->steps_data);
                $row->platform = $row->platform ?: PlatformType::ANDROID;
                $row->user_type = $row->user_type ?: UserType::ANONYMOUS;

                return $row;
            });

        $sessionStats = $this->sessionStats($start, $end);
        $rows = array();

        foreach ($this->groupDimensions() as $dimension) {
            $filteredFunnels = $funnels->filter(function ($row) use ($dimension) {
                return $this->matchesDimension($row, $dimension);
            })->values();

            $stats = $this->buildDimensionStats(
                $filteredFunnels,
                $sessionStats,
                $dimension,
                $targetDate
            );

            if (!empty($stats)) {
                $rows = array_merge($rows, $stats);
            }
        }

        DB::transaction(function () use ($targetDate, $rows) {
            DB::table('tracking_daily_stats')
                ->whereDate('stat_date', $targetDate->format('Y-m-d'))
                ->where('funnel_type', TrackingOrderFunnelDefinition::FUNNEL_TYPE)
                ->delete();

            if (!empty($rows)) {
                DB::table('tracking_daily_stats')->insert($rows);
            }
        });

        return count($rows);
    }

    protected function buildDimensionStats(Collection $funnels, array $sessionStats, array $dimension, Carbon $targetDate)
    {
        $steps = TrackingOrderFunnelDefinition::steps();
        $startedCount = $funnels->count();
        $completedFunnels = $funnels->filter(function ($row) {
            return $row->outcome === TrackingOrderOutcome::COMPLETED;
        })->values();
        $abandonedFunnels = $funnels->filter(function ($row) {
            return $row->outcome === TrackingOrderOutcome::ABANDONED;
        })->values();
        $stuckFunnels = $funnels->filter(function ($row) {
            return $row->outcome === TrackingOrderOutcome::IN_PROGRESS;
        })->values();
        $completionMinutes = $completedFunnels
            ->pluck('total_time_seconds')
            ->filter(function ($value) {
                return $value !== null;
            })
            ->map(function ($value) {
                return round(((int) $value) / 60, 2);
            })
            ->values()
            ->all();

        sort($completionMinutes);

        $summarySessionStats = $this->dimensionSessionStats($sessionStats, $dimension);
        if ($startedCount === 0 && (int) $summarySessionStats['total_sessions'] === 0) {
            return array();
        }
        $droppedMap = array();
        $stuckMap = array();

        foreach ($steps as $step) {
            $droppedMap[$step['name']] = 0;
            $stuckMap[$step['name']] = 0;
        }

        foreach ($abandonedFunnels as $funnel) {
            $dropStage = $this->dropStageName($funnel);
            if (isset($droppedMap[$dropStage])) {
                $droppedMap[$dropStage]++;
            }
        }

        foreach ($stuckFunnels as $funnel) {
            $stuckStage = $this->dropStageName($funnel);
            if (isset($stuckMap[$stuckStage])) {
                $stuckMap[$stuckStage]++;
            }
        }

        $summaryRow = $this->makeRow(
            $targetDate,
            $dimension,
            array(
                'step_name' => TrackingOrderFunnelDefinition::SUMMARY_STEP,
                'step_order' => 0,
                'started_count' => $startedCount,
                'reached_count' => $startedCount,
                'completed_count' => $completedFunnels->count(),
                'dropped_count' => $abandonedFunnels->count(),
                'conversion_pct' => $startedCount > 0
                    ? round(($completedFunnels->count() / $startedCount) * 100, 2)
                    : 0,
                'avg_completion_minutes' => $this->average($completionMinutes),
                'median_completion_minutes' => $this->median($completionMinutes),
                'stuck_count' => $stuckFunnels->count(),
                'meta_json' => array(
                    'dropoff' => $droppedMap,
                    'stuck' => $stuckMap,
                    'labels' => array(
                        'title' => 'Воронка оформления заказа',
                        'subtitle' => 'Агрегат по tracking_order_funnels',
                    ),
                ),
                'total_sessions' => $summarySessionStats['total_sessions'],
                'unique_ips' => $summarySessionStats['unique_ips'],
                'unique_devices' => $summarySessionStats['unique_devices'],
                'sessions_with_price_view' => $summarySessionStats['sessions_with_price_view'],
                'sessions_with_order_start' => $startedCount,
                'sessions_with_order_complete' => $completedFunnels->count(),
                'sessions_abandoned_at_order' => $abandonedFunnels->count(),
                'avg_session_duration_seconds' => $summarySessionStats['avg_session_duration_seconds'],
                'avg_order_completion_seconds' => $this->averageSeconds($completedFunnels->pluck('total_time_seconds')->all()),
                'total_revenue' => (int) $completedFunnels->pluck('order_price')->filter()->sum(),
                'total_orders' => $completedFunnels->count(),
            )
        );

        $rows = array($summaryRow);

        foreach ($steps as $step) {
            $reachedCount = $step['name'] === 'completed'
                ? $completedFunnels->count()
                : $funnels->filter(function ($row) use ($step) {
                    return $this->stepCompleted($row, $step['order']);
                })->count();

            $rows[] = $this->makeRow(
                $targetDate,
                $dimension,
                array(
                    'step_name' => $step['name'],
                    'step_order' => $step['order'],
                    'started_count' => $startedCount,
                    'reached_count' => $reachedCount,
                    'completed_count' => $completedFunnels->count(),
                    'dropped_count' => $droppedMap[$step['name']],
                    'conversion_pct' => $startedCount > 0
                        ? round(($reachedCount / $startedCount) * 100, 2)
                        : 0,
                    'avg_completion_minutes' => $this->average($completionMinutes),
                    'median_completion_minutes' => $this->median($completionMinutes),
                    'stuck_count' => $stuckMap[$step['name']],
                    'meta_json' => array(
                        'label' => $step['label'],
                        'description' => $step['description'],
                    ),
                    'total_sessions' => 0,
                    'unique_ips' => 0,
                    'unique_devices' => 0,
                    'sessions_with_price_view' => 0,
                    'sessions_with_order_start' => 0,
                    'sessions_with_order_complete' => 0,
                    'sessions_abandoned_at_order' => 0,
                    'avg_session_duration_seconds' => 0,
                    'avg_order_completion_seconds' => 0,
                    'total_revenue' => 0,
                    'total_orders' => 0,
                )
            );
        }

        return $rows;
    }

    protected function makeRow(Carbon $date, array $dimension, array $payload)
    {
        return array(
            'stat_date' => $date->format('Y-m-d 00:00:00'),
            'platform' => $dimension['platform'],
            'user_type' => $dimension['user_type'],
            'funnel_type' => TrackingOrderFunnelDefinition::FUNNEL_TYPE,
            'step_name' => $payload['step_name'],
            'step_order' => $payload['step_order'],
            'started_count' => (int) $payload['started_count'],
            'reached_count' => (int) $payload['reached_count'],
            'completed_count' => (int) $payload['completed_count'],
            'dropped_count' => (int) $payload['dropped_count'],
            'conversion_pct' => (float) $payload['conversion_pct'],
            'avg_completion_minutes' => $payload['avg_completion_minutes'],
            'median_completion_minutes' => $payload['median_completion_minutes'],
            'stuck_count' => (int) $payload['stuck_count'],
            'meta_json' => json_encode($payload['meta_json'], JSON_UNESCAPED_UNICODE),
            'total_sessions' => (int) $payload['total_sessions'],
            'unique_ips' => (int) $payload['unique_ips'],
            'unique_devices' => (int) $payload['unique_devices'],
            'sessions_with_price_view' => (int) $payload['sessions_with_price_view'],
            'sessions_with_order_start' => (int) $payload['sessions_with_order_start'],
            'sessions_with_order_complete' => (int) $payload['sessions_with_order_complete'],
            'sessions_abandoned_at_order' => (int) $payload['sessions_abandoned_at_order'],
            'avg_session_duration_seconds' => (int) $payload['avg_session_duration_seconds'],
            'avg_order_completion_seconds' => (int) $payload['avg_order_completion_seconds'],
            'total_revenue' => (int) $payload['total_revenue'],
            'total_orders' => (int) $payload['total_orders'],
            'created_at' => now(),
            'updated_at' => now(),
        );
    }

    protected function sessionStats(Carbon $start, Carbon $end)
    {
        $sessionRows = DB::table('tracking_sessions')
            ->select(
                'id',
                'platform',
                'user_type',
                'ip_address',
                'device_id',
                'duration_seconds'
            )
            ->whereBetween('created_at', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->get();

        $priceViewSessionIds = DB::table('tracking_events')
            ->whereBetween('created_at', array(
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s'),
            ))
            ->whereIn('event_type', array(
                'calculate_price',
                'price_calculated',
                'price_viewed',
            ))
            ->pluck('session_id')
            ->unique()
            ->flip()
            ->all();

        return array(
            'sessions' => $sessionRows,
            'price_view_session_ids' => $priceViewSessionIds,
        );
    }

    protected function dimensionSessionStats(array $sessionStats, array $dimension)
    {
        $sessions = collect($sessionStats['sessions'])->filter(function ($row) use ($dimension) {
            return $this->matchesDimension($row, $dimension);
        })->values();

        $durations = $sessions->pluck('duration_seconds')->filter(function ($value) {
            return $value !== null;
        })->all();

        $priceViewSessionIds = $sessionStats['price_view_session_ids'];

        return array(
            'total_sessions' => $sessions->count(),
            'unique_ips' => $sessions->pluck('ip_address')->filter()->unique()->count(),
            'unique_devices' => $sessions->pluck('device_id')->filter()->unique()->count(),
            'sessions_with_price_view' => $sessions->filter(function ($row) use ($priceViewSessionIds) {
                return isset($priceViewSessionIds[$row->id]);
            })->count(),
            'avg_session_duration_seconds' => $this->averageSeconds($durations),
        );
    }

    protected function normalizeSteps($stepsData)
    {
        if (is_array($stepsData)) {
            return $stepsData;
        }

        if (is_string($stepsData) && $stepsData !== '') {
            $decoded = json_decode($stepsData, true);
            return is_array($decoded) ? $decoded : array();
        }

        return array();
    }

    protected function stepCompleted($funnel, $stepOrder)
    {
        if ($stepOrder === 4) {
            return $funnel->outcome === TrackingOrderOutcome::COMPLETED;
        }

        if (!isset($funnel->steps_data[$stepOrder])) {
            return false;
        }

        return !empty($funnel->steps_data[$stepOrder]['completed_at']);
    }

    protected function dropStageName($funnel)
    {
        $steps = TrackingOrderFunnelDefinition::steps();

        if ($funnel->outcome === TrackingOrderOutcome::COMPLETED) {
            return 'completed';
        }

        for ($stepOrder = 3; $stepOrder >= 1; $stepOrder--) {
            if ($this->stepCompleted($funnel, $stepOrder)) {
                if ($stepOrder === 3) {
                    return 'completed';
                }

                return $steps[$stepOrder]['name'];
            }
        }

        return 'route_filled';
    }

    protected function groupDimensions()
    {
        $dimensions = array();

        foreach (array('all', PlatformType::ANDROID, PlatformType::IOS, PlatformType::WEB) as $platform) {
            foreach (array('all', UserType::REGISTERED, UserType::ANONYMOUS) as $userType) {
                $dimensions[] = array(
                    'platform' => $platform,
                    'user_type' => $userType,
                );
            }
        }

        return $dimensions;
    }

    protected function matchesDimension($row, array $dimension)
    {
        $platformOk = $dimension['platform'] === 'all' || $row->platform === $dimension['platform'];
        $userTypeOk = $dimension['user_type'] === 'all' || $row->user_type === $dimension['user_type'];

        return $platformOk && $userTypeOk;
    }

    protected function average(array $values)
    {
        if (empty($values)) {
            return null;
        }

        return round(array_sum($values) / count($values), 2);
    }

    protected function averageSeconds(array $values)
    {
        $filtered = array_filter($values, function ($value) {
            return $value !== null;
        });

        if (empty($filtered)) {
            return 0;
        }

        return (int) round(array_sum($filtered) / count($filtered));
    }

    protected function median(array $values)
    {
        if (empty($values)) {
            return null;
        }

        $count = count($values);
        $middle = (int) floor($count / 2);

        if ($count % 2) {
            return round((float) $values[$middle], 2);
        }

        return round(((float) $values[$middle - 1] + (float) $values[$middle]) / 2, 2);
    }
}
