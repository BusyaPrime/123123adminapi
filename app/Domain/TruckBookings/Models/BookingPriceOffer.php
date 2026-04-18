<?php

namespace App\Domain\TruckBookings\Models;

use Illuminate\Database\Eloquent\Model;

use App\Domain\Users\Models\User;

class BookingPriceOffer extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['booking_id', 'driver_id', 'amount', 'client_amount'];

    /**
     * Get the driver that owns the BookingPriceOffer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }
}
