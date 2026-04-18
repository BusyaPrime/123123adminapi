<?php


namespace App\Domain\TruckBookings\Jobs;

use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\TruckBookings\Exceptions\CompanyNotFoundForPaymentException;
use App\Domain\TruckBookings\Requests\TruckBookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class TruckBookingJob
{
    use Queueable, Dispatchable;

    /**
     * @var TruckBookingRequest
     */
    protected $request;
    protected $carType;

    /**
     * TruckBookingJob constructor.
     * @param TruckBookingRequest $request
     */
    public function __construct(TruckBookingRequest $request, CarType $carType)
    {
        $this->request = $request;
        $this->carType = $carType;
    }

    /**
     * @return TruckBooking
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $booking = new TruckBooking();
            $authUser = $this->request->input('authUser');
            $carType = $this->carType??$booking->carType;
            $paymentType = $this->request->filled('payment_type')
                ? $this->request->input('payment_type')
                : TruckBooking::PAY_CASH;
            $companyId = null;

            if ($paymentType === TruckBooking::PAY_COMPANY) {
                $companyId = $authUser ? $authUser->resolveClientCompanyId() : null;

                if (!$companyId) {
                    throw new CompanyNotFoundForPaymentException();
                }
            }

            $booking->routes = json_encode($this->request->input('routes', []));
            $booking->user_id = $authUser->id;
            $booking->driver_id = $this->request->input('driver_id', null);
            $booking->car_type_id = $this->request->input('car_type_id', null);
            $booking->region_from_id = $this->request->input('region_from_id', null);
            $booking->region_to_id = $this->request->input('region_to_id', null);
            $booking->client_company_id = $companyId;
            $booking->price = 0;
            $booking->commission = 0;
            $booking->last_notification_reminder = time();
            $booking->cargo_type_id = $this->request->input('cargo_type_id', null);
            $booking->load_type_id = $this->request->input('load_type_id', null);
            $booking->weight = $this->request->input('weight', null);
            $booking->dimension_x = $this->request->input('dimension_x', null);
            $booking->dimension_y = $this->request->input('dimension_y', null);
            $booking->dimension_z = $this->request->input('dimension_z', null);
            $booking->receiver_phone = $this->request->input('receiver_phone', null);
            $booking->need_pack = $this->request->input('need_pack', 0);
            $booking->need_provide_loader = $this->request->input('need_provide_loader', 0);
            $booking->loader_amount = $this->request->input('loader_amount', null);
            $booking->comment = $this->request->input('comment', null);
            $booking->distance = $this->request->input('distance', 0);
            $booking->status = TruckBooking::STATUS_ORDER;
            $booking->payment_type = $paymentType;
            $booking->not_full = 0;
            $booking->accepting_time = null;
            $booking->driving_time = null;
            $booking->instant = $this->request->input('instant', 0);
            $booking->payer = $this->request->input('payer', 'sender');

            $booking->pickup_limit = 0;
            $booking->pickup_per_minute = 0;
            $booking->pickup_waiting_time = null;
            $booking->way_in_progress = 0;
            $booking->pickup_overtime = 0;
            $booking->pickup_price = 0;
            $booking->unloading_limit = 0;
            $booking->unloading_per_minute = 0;
            $booking->unloading_waiting_time = null;
            $booking->unloading_overtime = 0;
            $booking->unloading_price = 0;

            $booking->delivery_price = 0;

            if ($this->request->filled('pickup_date')) {
                $booking->pickup_date = date('Y-m-d', strtotime($this->request->input('pickup_date', date('Y-m-d'))));
            } else {
                $booking->pickup_date = date('Y-m-d');
            }

            $date = $this->request->input('date', date('Y-m-d'));
            $time = $this->request->input('time', '00:00');
            $booking->pickup_date = $date;
            $booking->pickup_time = $time;

            $booking->save();

            $date = $this->request->input('date', date('Y-m-d'));
            $time = $this->request->input('time', '00:00');

            if ($carType) {
                $booking->pickup_limit = $carType->pickup_limit;
                $booking->pickup_per_minute = $carType->pickup_per_minute;
                $booking->unloading_limit = $carType->unloading_limit;
                $booking->unloading_per_minute = $carType->unloading_per_minute;
            }

            if($this->request->has('price') && $this->request->input('price') > 0) {
                $booking->price = (int)preg_replace('/[^\d]/', '', $this->request->input('price', $booking->price));
            } else {
                $booking->price = $carType->calculatedPrice;
            }


            if ($booking->payment_type !== TruckBooking::PAY_COMPANY) {
                $booking->commission = ceil($booking->price * $carType->commission / 100);
            }
            
            $booking->delivery_price = $booking->price;
            $booking->is_round_trip = $this->request->input('is_full_round_trip', 0);
            $booking->distance = $booking->is_round_trip == 1 ? $booking->distance * 2 : $booking->distance;

  
            if (true === $carType->notFullEnabled && $carType->notFullEnabledPercentage > 0 && $carType->notFullEnabledPercentage < 100) {
                $booking->partial_percentage = $carType->notFullEnabledPercentage;
            }
            if ($this->request->input('car_size_id', 0)) {
                $booking->car_size_id = $this->request->input('car_size_id');
            }

            $booking->recomended_price = $carType->calculatedPrice;
            
            if($booking->payment_type === TruckBooking::PAY_COMPANY) {
                $booking->driver_price = floor(($booking->price * (1 - TruckBooking::BANK_TRANSFER_COEFFICIENT)) / 1000) * 1000;
            } else {
                $booking->driver_price = $booking->price;
            }

            $booking->save();
            $booking->findAndAttachCars();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $booking;
    }
}
