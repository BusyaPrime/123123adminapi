<?php

namespace App\Domain\TruckBookings\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Cars\Models\Car;

class TruckBookingView extends Model
{
    public function driver(){
        return $this->belongsTo(Car::class, 'driver_id', 'user_id')->with('user');
    }
}
