<?php

namespace App\Domain\TruckBookings\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Domain\TruckBookings\Models\TruckBooking;

class InternalOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bookID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bookID)
    {
        $this->bookID = $bookID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $booking = TruckBooking::find($this->bookID);
            if ($booking && $booking->status == TruckBooking::STATUS_ORDER) {
                $booking->driver_id = 414;
                $booking->status = TruckBooking::STATUS_ACCEPTED;
                $booking->save();
                \Log::info('Internal order job handled successfully!');
            }
        } catch (\Throwable $th) {
            \Log::error('Error');
            throw new \Exception($th);
        }
    }
}
