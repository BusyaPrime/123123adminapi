<?php


namespace App\Domain\TCarBookings\Jobs;


use App\Domain\TCarBookings\Models\TcarBooking;
use App\Domain\TCarBookings\Requests\TcarBookingRequest;
use App\Domain\Tcars\Models\Tcar;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class TcarBookingJob
{
    use Queueable, Dispatchable;

    protected $request;

    public function __construct(TcarBookingRequest $request)
    {
        $this->request = $request;
    }


    /**
     * @return TcarBooking
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $booking = new TcarBooking();

            $booking->routes = json_encode($this->request->input('routes', []));
            $booking->user_id = $this->request->input('authUser')->id;
            $booking->driver_id = $this->request->input('driver_id', null);
            $booking->tcar_type_id = $this->request->input('car_type_id', null);
            $booking->price = 0;
            $booking->commission = 0;
            $booking->peoples = $this->request->input('peoples', 1);
            $booking->date = $this->request->input('date', null);
            $booking->time = $this->request->input('time', null);
            $booking->ac = $this->request->input('ac', 0);
            $booking->kids_seat = $this->request->input('kids_seat', 0);
            $booking->comment = $this->request->input('comment', null);
            $booking->distance = $this->request->input('distance', null);
            $booking->status = TcarBooking::STATUS_NEW;
            $booking->payment_type = $this->request->input('payment_type', TcarBooking::PAY_CASH);

            $booking->save();

            if ($this->request->filled('price')) {
                $booking->price = $this->request->input('price', $booking->carType->min_price);
            } else {
                if ($booking->distance <= $booking->carType->min_distance) {
                    $booking->price = $booking->carType->min_price;
                } else {
                    $booking->price = ceil($booking->carType->min_price + (((int) $booking->distance - $booking->carType->min_distance) * $booking->carType->price_per_km));
                }
            }
            $booking->commission = ceil($booking->price * $booking->carType->commission / 100);

            $booking->save();
            $booking->findAndAttachCars();

        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $booking;
    }
//
//
//    protected function findCars($booking)
//    {
//        $builder = Tcar::where('active', 1)
//            ->where('tcar_type_id', $booking->tcar_type_id);
//
//        if ($booking->peoples) {
//            $builder->where('peoples', '>=', $booking->peoples);
//        }
//
//        if ($booking->ac) {
//            $builder->where('ac', $booking->ac);
//        }
//        if ($booking->kids_seat) {
//            $builder->where('kids_seat', $booking->kids_seat);
//        }
//        $cars = $builder->get()->keyBy('id')->toArray();
//
//        return array_keys($cars);
//    }
}
