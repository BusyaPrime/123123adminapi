<?php


namespace App\Events;


use App\Domain\TruckBookings\Resources\TruckBookingResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderStatusEvent implements ShouldBroadcast
{
    use SerializesModels;

    public $booking;
    public $car;
    public $phone;
    public $channel;

    public function __construct($booking, $car, $phone, $channel)
    {
        $this->booking = TruckBookingResource::make($booking);
        $this->booking->routes = $this->booking->parseRoutes();
        $this->car = $car;
        $this->phone = $phone;
        $this->channel = $channel;
    }

    public function broadcastAs()
    {
        return 'OrderStatusEvent';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        return new Channel('truck_order.'.$this->channel);
    }
}
