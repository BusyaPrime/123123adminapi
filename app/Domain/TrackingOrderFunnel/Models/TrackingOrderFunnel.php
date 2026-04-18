<?php

namespace App\Domain\TrackingOrderFunnel\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\TrackingSession\Models\TrackingSession;
use App\Domain\Users\Models\User;

class TrackingOrderFunnel extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'order_id',
        'from_address',
        'to_address',
        'calculated_price',
        'cargo_type_id',
        'has_changed_price',
        'changed_price',
        'car_type_id',
        'cargo_weight',
        'max_step_reached',
        'total_steps',
        'steps_data',
        'outcome',
        'abandoned_at_step_name',
        'ended_at',
        'abandoned_at',
        'total_time_seconds',
    ];

    protected $casts = [
        'steps_data' => 'array',
        'created_at' => 'datetime',
        'ended_at' => 'datetime',
        'abandoned_at' => 'datetime',
    ];

    protected $guarded = ['id'];

    public function session()
    {
        return $this->belongsTo(TrackingSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
