<?php


namespace App\Domain\Messages\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMessageJob
{
    use Queueable, Dispatchable;

    protected $message;

    protected $booking;

    public function __construct($message, $booking)
    {
        $this->message = $message;
        $this->booking = $booking;
    }


    /**
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $booking = $this->booking;
            $booking->messages()->save($this->message);
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $this->message;
    }
}
