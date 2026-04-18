<?php


namespace App\Events;


use Illuminate\Queue\SerializesModels;

class StartOrderListUpdatingEvent
{
    use SerializesModels;

    public $cars;

    public function __construct($cars)
    {
        $this->cars = $cars;
    }
}
