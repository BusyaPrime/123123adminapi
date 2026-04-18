<?php


namespace App\Domain\TruckBookings\Resources;


use App\Domain\CargoTypes\Resources\CargoTypeResource;
use App\Domain\CarTypes\Resources\CarTypeResource;
use App\Domain\LoadTypes\Resources\LoadTypeResource;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Resources\UserProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TruckBookingResource
 * @package App\Domain\TruckBookings\Resources
 * @mixin TruckBooking
 */
class TruckBookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'routes' => $this->parseRoutes(),

            'user_id' => $this->user_id,
            'driver_id' => $this->driver_id,

            'car_type_id' => $this->car_type_id,
            'cargo_type_id' => $this->cargo_type_id,
            'load_type_id' => $this->load_type_id,

            'user' => $this->whenLoaded('user', UserProfileResource::make($this->user)),
            'driver' => $this->whenLoaded('driver', UserProfileResource::make($this->driver)),
            'car' => $this->getDriverCar(),
            'car_type' => $this->whenLoaded('carType', CarTypeResource::make($this->carType)),
            'cargo_type' => $this->whenLoaded('cargoType', CargoTypeResource::make($this->cargoType)),
            'load_type' => $this->whenLoaded('loadType', LoadTypeResource::make($this->loadType)),
            'booking_price_offer' => $this->whenLoaded('bookingPriceOffer', $this->bookingPriceOffer),

            'pickup_limit' => $this->pickup_limit,
            'pickup_per_minute' => $this->pickup_per_minute,
            'pickup_waiting_time' => $this->pickup_waiting_time,
            'pickup_overtime' => $this->pickup_overtime,
            'pickup_price' => $this->pickup_price,

            'unloading_limit' => $this->unloading_limit,
            'unloading_per_minute' => $this->unloading_per_minute,
            'unloading_waiting_time' => $this->unloading_waiting_time,
            'unloading_overtime' => $this->unloading_overtime,
            'unloading_price' => $this->unloading_price,
            'delivery_price' => $this->delivery_price,
            'price' => $this->price,
            'driver_price' => $this->driver_price,

            'commission' => $this->commission,
            'weight' => (string) $this->weight,
            'not_full' => (bool)$this->not_full,
            'dimension_x' => $this->dimension_x,
            'dimension_y' => $this->dimension_y,
            'dimension_z' => $this->dimension_z,
            'need_pack' => $this->need_pack ? true: false,
            'need_provide_loader' => $this->need_provide_loader ? true: false,
            'loader_amount' => $this->loader_amount,
            'comment' => $this->comment ?? '',
            'rating' => $this->rating,
            'review' => $this->review,
            'driver_rating' => $this->driver_rating,
            'driver_review' => $this->driver_review,
            'distance' => (string) $this->distance,
            'created_at' => $this->created_at->format('d.m.Y H:i'),
            'pickup_date' => date('d.m.Y H:i', strtotime($this->pickup_date.' '.$this->pickup_time)),
            'status' => $this->status,
            'waiting_time' => $this->waiting_time ? $this->waiting_time: null,
            'payment_type' => $this->payment_type,
            'instant' => $this->instant,
            'receiver_phone' => $this->receiver_phone,
            'payer' => $this->payer,
            'is_round_trip' => (int) $this->is_round_trip,
            'is_one_way_complete' => $this->is_one_way_complete,
            'way_in_progress' => $this->way_in_progress,
            'round_pickup_overtime' => $this->round_pickup_overtime,
            'round_pickup_price' => $this->round_pickup_price,
            'round_unloading_overtime' => $this->round_unloading_overtime,
            'round_unloading_price' => $this->round_unloading_price,
            'partial_percentage' => $this->partial_percentage,
            'car_size_id' => $this->car_size_id,
            'views' => $this->views()->count(),
            'additional_price' => $this->additional_price,
            'additional_price_review' => $this->additional_price_review,
        ];
		
    }
}
