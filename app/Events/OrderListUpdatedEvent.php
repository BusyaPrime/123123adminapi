<?php


namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderListUpdatedEvent implements ShouldBroadcast
{
    use SerializesModels;

    public $bookings;
    public $channel;

    public function __construct($bookings = [], $channel)
    {
        $this->bookings = $bookings;
        $this->channel = $channel;
    }

    public function broadcastAs()
    {
        return 'OrderListUpdatedEvent';
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        return new Channel('order_list.'.$this->channel);
    }
}
