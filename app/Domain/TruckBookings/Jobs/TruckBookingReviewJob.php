<?php


namespace App\Domain\TruckBookings\Jobs;


use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\TruckBookings\Requests\TruckBookingReviewRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class TruckBookingReviewJob
{
    use Queueable, Dispatchable;

    protected $request;
    protected $booking;

    public function __construct(TruckBookingReviewRequest $request, TruckBooking $booking)
    {
        $this->request = $request;
        $this->booking = $booking;
    }


    /**
     * @return TruckBooking
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $booking = $this->booking;

            $booking->rating = $this->request->input('rating', null);
            $booking->review = $this->request->input('review', null);

            $booking->save();
            $booking->refresh();

            if($booking->driver) {
                $booking->driver->rating = round(TruckBooking::where('driver_id', $booking->driver->user_id)
                    ->whereNotNull('rating')->average('rating'), 1);
                $booking->driver->save();
                if($booking->driver->company) {
                    $booking->driver->company->rating = round(TruckBooking::where('company_id', $booking->driver->company->id)
                        ->whereNotNull('rating')->average('rating'), 1);
                    $booking->driver->company->save();
                }
            }
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $booking;
    }
}
