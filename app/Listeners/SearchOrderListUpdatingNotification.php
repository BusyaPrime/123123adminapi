<?php

namespace App\Listeners;

use App\Domain\Cars\Models\Car;
use App\Domain\TCarBookings\Models\TcarBooking;
use App\Domain\TCarBookings\Resources\TcarBookingResource;
use App\Domain\Tcars\Models\Tcar;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\TruckBookings\Resources\TruckBookingResource;
use App\Events\OrderListUpdatedEvent;
use App\Events\TOrderListUpdatedEvent;

class SearchOrderListUpdatingNotification
{
    public function handle($event)
    {
        $cars = $event->cars;
        foreach ($cars as $car) {
            if ($car instanceof Car) {
                $bookings = TruckBookingResource::collection($car->bookings()
                    ->where('status', TruckBooking::STATUS_ORDER)
                    ->withCarType()
                    ->withCargoType()
                    ->withLoadType()
                    ->with('user', 'driver')->get());
                event(new OrderListUpdatedEvent($bookings, $car->id));
            } else if($car instanceof Tcar)
            {
                $bookings = TcarBookingResource::collection($car->bookings()
                    ->where('status', TcarBooking::STATUS_ORDER)
                    ->withCarType()
                    ->with('user', 'driver')
                    ->orderBy('created_at', 'DESC')->get());
                event(new TOrderListUpdatedEvent($bookings, $car->id));
            }
        }
    }
}
