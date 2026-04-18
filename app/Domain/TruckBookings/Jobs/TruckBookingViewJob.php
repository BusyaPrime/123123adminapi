<?php

namespace App\Domain\TruckBookings\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Domain\TruckBookings\Models\TruckBookingView;

class TruckBookingViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $driver_id;
    protected $booking_id;

    public function __construct($driver_id, $booking_id)
    {
        $this->driver_id = $driver_id;
        $this->booking_id = $booking_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
                $view = new TruckBookingView();
                $view->driver_id = $this->driver_id;
                $view->truck_booking_id = $this->booking_id;
                $view->save();
        } catch (\Throwable $th) {
            \DB::rollBack();
        }
        \DB::commit();
    }
}
