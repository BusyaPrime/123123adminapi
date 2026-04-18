<?php


namespace App\Events;


use Illuminate\Queue\SerializesModels;

class SearchStartEvent
{

    use SerializesModels;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }
}
