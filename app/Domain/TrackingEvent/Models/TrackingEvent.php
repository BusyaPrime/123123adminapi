<?php

namespace App\Domain\TrackingEvent\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\TrackingSession\Models\TrackingSession;
use App\Domain\Users\Models\User;

class TrackingEvent extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'event_type',
        'screen_name',
        'properties',
        'funnel_step',
        'funnel_name',
        'order_id',
        'value',
        'time_since_session_start'
    ];

    protected $casts = [
        'properties' => 'array',
        'value' => 'int'
    ];

    public function session()
    {
        return $this->belongsTo(TrackingSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Event type constants
    const TYPE_PAGE_VIEW = 'page_view';
    const TYPE_APP_OPEN = 'app_open';
    const TYPE_APP_CLOSE = 'app_close';
    const TYPE_PRICE_CALCULATOR_OPEN = 'price_calculator_open';
    const TYPE_PRICE_CALCULATED = 'price_calculated';
    const TYPE_PRICE_VIEWED = 'price_viewed';
    const TYPE_ORDER_START = 'order_start';
    const TYPE_ORDER_STEP_COMPLETE = 'order_step_complete';
    const TYPE_ORDER_STEP_BACK = 'order_step_back';
    const TYPE_ORDER_ABANDONED = 'order_abandoned';
    const TYPE_ORDER_CREATED = 'order_created';
    const TYPE_ORDER_PAID = 'order_paid';
    const TYPE_REGISTRATION_START = 'registration_start';
    const TYPE_REGISTRATION_COMPLETE = 'registration_complete';
    const TYPE_REGISTRATION_ABANDONED = 'registration_abandoned';
    const TYPE_LOGIN_SUCCESS = 'login_success';
    const TYPE_LOGIN_FAILED = 'login_failed';
    const TYPE_BUTTON_CLICK = 'button_click';
    const TYPE_FORM_ERROR = 'form_error';
    const TYPE_SEARCH_PERFORMED = 'search_performed';
    const TYPE_MAP_INTERACTION = 'map_interaction';
    const TYPE_ERROR_SHOWN = 'error_shown';
    const TYPE_API_ERROR = 'api_error';
}
