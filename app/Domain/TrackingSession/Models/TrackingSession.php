<?php

namespace App\Domain\TrackingSession\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\User;
use App\Domain\TrackingEvent\Models\TrackingEvent;
use App\Domain\TrackingOrderFunnel\Models\TrackingOrderFunnel;
use Illuminate\Support\Str;

class TrackingSession extends Model {
    protected $fillable = [
        'session_token',
        'user_id', 'user_type',
        'platform', 'app_version',
        'ip_address', 'user_agent', 'browser', 'browser_version',
        'os', 'device_id', 'device_model', 'device_type',
        'referrer_url', 'utm_source', 'utm_medium', 'utm_campaign',
        'country', 'country_code', 'city', 'lat', 'lng',
        'language', 'timezone',
        'status', 'resulted_in_order', 'order_id',
        'ended_at', 'duration_seconds', 'last_seen_at',
    ];

    protected $casts = [
        'resulted_in_order' => 'boolean',
        'ended_at'          => 'datetime',
        'last_seen_at'      => 'datetime',
        'lat'               => 'float',
        'lng'               => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->session_token)) {
                $model->session_token = Str::random(64);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->hasMany(TrackingEvent::class, 'session_id');
    }

    public function orderFunnels()
    {
        return $this->hasMany(TrackingOrderFunnel::class, 'session_id');
    }

    public function markCompleted()
    {
        $duration = now()->diffInSeconds($this->started_at);
        
        $this->update([
            'status'           => 'completed',
            'ended_at'         => now(),
            'last_seen_at'     => now(),
            'duration_seconds' => $duration,
        ]);
    }

    public function touchSession()
    {
        $this->update(['last_seen_at' => now()]);
    }
}
