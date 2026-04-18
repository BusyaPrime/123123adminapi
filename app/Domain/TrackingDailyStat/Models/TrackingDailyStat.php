<?php

namespace App\Domain\TrackingDailyStat\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingDailyStat extends Model
{
    protected $fillable = [
        'stat_date',
        'platform',
        'user_type',
        'funnel_type',
        'step_name',
        'step_order',
        'started_count',
        'reached_count',
        'completed_count',
        'dropped_count',
        'conversion_pct',
        'avg_completion_minutes',
        'median_completion_minutes',
        'stuck_count',
        'meta_json',
        'total_sessions',
        'unique_ips',
        'unique_devices',
        'sessions_with_price_view',
        'sessions_with_order_start',
        'sessions_with_order_complete',
        'sessions_abandoned_at_order',
        'avg_session_duration_seconds',
        'avg_order_completion_seconds',
        'total_revenue',
        'total_orders',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'conversion_pct' => 'float',
        'meta_json' => 'array',
    ];

    public function scopeForFunnel($query, $funnelType)
    {
        return $query->where('funnel_type', $funnelType);
    }

    public function scopeSummaryRows($query)
    {
        return $query->where('step_name', '__summary');
    }
}
