<?php


namespace App\Domain\TCarBookings\Resources;


use App\Domain\TCarBookings\Models\TcarBooking;
use App\Domain\TCarTypes\Resources\TcarTypeResource;
use App\Domain\Users\Resources\UserProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TcarBookingResource
 * @package App\Domain\TCarBookings\Resources
 * @mixin TcarBooking
 */
class TcarBookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'routes' => $this->parseRoutes(),
            'user_id' => $this->user_id,
            'driver_id' => $this->driver_id,
            'car_type_id' => $this->tcar_type_id,
            'user' => $this->whenLoaded('user', UserProfileResource::make($this->user)),
            'driver' => $this->whenLoaded('driver', UserProfileResource::make($this->driver)),
            'car' => $this->getDriverCar(),
            'car_type' => $this->whenLoaded('carType', TcarTypeResource::make($this->carType)),
            'peoples' => $this->peoples,
            'price' => $this->price,
            'commission' => $this->commission,
            'date' => $this->date,
            'time' => $this->time,
            'ac' => $this->ac ? true: false,
            'kids_seat' => $this->kids_seat ? true: false,
            'rating' => $this->rating,
            'review' => $this->review,
            'comment' => $this->comment,
            'distance' => $this->distance,
            'created_at' => $this->created_at->format('d.m.Y H:i'),
            'status' => $this->status,
            'waiting_time' => $this->waiting_time ? $this->waiting_time: null,
            'payment_type' => $this->payment_type
        ];
    }
}
