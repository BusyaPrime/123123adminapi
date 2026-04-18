<?php


namespace App\Domain\TCarBookings\Jobs;


use App\Domain\TCarBookings\Models\TcarBooking;
use App\Domain\TCarBookings\Requests\TcarBookingReviewRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class TcarBookingReviewJob
{
    use Queueable, Dispatchable;


    protected $request;
    protected $booking;

    public function __construct(TcarBookingReviewRequest $request, TcarBooking $booking)
    {
        $this->request = $request;
        $this->booking = $booking;
    }


    /**
     * @return TcarBooking
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $booking = $this->booking;

            $booking->rating = $this->request->input('rating', null);
            $booking->review = $this->request->input('review', null);

            $booking->driver->rating = TcarBooking::where('driver_id', $booking->driver->id)
                ->whereNotNull('rating')->average('rating');

            $booking->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $booking;
    }
}
