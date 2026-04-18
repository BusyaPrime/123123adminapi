<?php

namespace App\Domain\AdminRoles\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public static function permissionsList()
    {
        return [
            'users',
            'companies',
            'cars',
            'bookings',
            'transactions',
            'admins',
            'seasons',
            'car-types',
            'sizes',
            'regions',
            'load-types',
            'cargo-types',
            'commission-rates',
            'statistics',
            'reviews',
            'chat',
            'contacts',
            'articles',
            'tracking',
            'cancel_reasons',
            'ticket_themes',
            'tickets',
            'appversion',
            'company_priorities',
            'rates_history',
            'additional_price',
            'frontend',
            'system',
            'confirmation-codes',
            'change_booking_state',
            'driver_price_offers',
        ];
    }
}
