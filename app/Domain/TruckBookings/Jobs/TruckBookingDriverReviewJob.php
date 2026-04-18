<?php


namespace App\Domain\TruckBookings\Jobs;


use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\TruckBookings\Requests\TruckBookingReviewRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class TruckBookingDriverReviewJob
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

            $booking->driver_rating = $this->request->input('rating', null);
            $booking->driver_review = $this->request->input('review', null);

            $booking->save();
            $booking->refresh();

            if($booking->user) {
                $booking->user->rating = round(TruckBooking::where('user_id', $booking->user->user_id)
                    ->whereNotNull('rating')->average('rating'), 1);
                $booking->user->save();
            }
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $booking;
    }
}
