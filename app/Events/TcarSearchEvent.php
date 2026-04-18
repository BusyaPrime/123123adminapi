<?php


namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TcarSearchEvent implements ShouldBroadcast
{

    use SerializesModels;

    public $booking;
    public $channel;

    public function __construct($booking, $channel)
    {
        $this->booking = $booking;
        $this->channel = $channel;
    }

    public function broadcastAs()
    {
        return 'TcarSearchEvent';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        return new Channel('car.'.$this->channel);
    }
}
