<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DriverOrderlistUpdatedEvent implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
     public $status;
     public $driver_id;

    public function __construct($status, $driver_id)
    {
        $this->status = $status;
        $this->driver_id = $driver_id;
    }

    public function broadcastAs()
    {
        return 'DriverOrderlistUpdatedEvent';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('truck_orders.'.$this->driver_id);
    }
}
