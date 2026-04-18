<?php

namespace App\Domain\TruckBookings\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

use App\Domain\TruckBookings\Requests\TruckBookingStatusRequest;
use App\Domain\TruckBookings\Models\TruckBooking;

class AutoConfirmBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $booking;
     protected $request;

    public function __construct(TruckBooking $booking, $request)
    {
        $this->request = $request;
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            $booking = TruckBooking::lockForUpdate()->find($this->booking->id);

            if (!$booking || $booking->status !== TruckBooking::STATUS_CONFIRMATION) {
                return;
            }

            if ($booking->status == TruckBooking::STATUS_CONFIRMATION) {
                $request = new TruckBookingStatusRequest();
                $request->merge(['update_from_client' => 1, 'status' => TruckBooking::STATUS_DONE]);
                
                app()->call(
                    [app(\App\Http\Controllers\Api\TruckBookingController::class), 'changeState'],
                    ['truckBooking' => $this->booking, 'request' => $request]
                );
            }
        });
    }
}
