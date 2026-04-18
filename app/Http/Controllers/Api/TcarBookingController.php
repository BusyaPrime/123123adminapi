<?php


namespace App\Http\Controllers\Api;


use App\Domain\Messages\Jobs\SendMessageJob;
use App\Domain\Messages\Models\TcarMessage;
use App\Domain\Messages\Requests\SendMessageRequest;
use App\Domain\Messages\Resuorces\MessageResource;
use App\Domain\TCarBookings\Jobs\TcarBookingJob;
use App\Domain\TCarBookings\Jobs\TcarBookingReviewJob;
use App\Domain\TCarBookings\Models\TcarBooking;
use App\Domain\TCarBookings\Requests\TcarBookingRequest;
use App\Domain\TCarBookings\Requests\TcarBookingReviewRequest;
use App\Domain\TCarBookings\Requests\TcarBookingStatusRequest;
use App\Domain\TCarBookings\Resources\TcarBookingResource;
use App\Domain\Tcars\Models\Tcar;
use App\Events\SearchStartEvent;
use App\Events\StartOrderListUpdatingEvent;
use App\Events\TcarOrderStatusEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TcarBookingController extends Controller
{
    public function getBookingsInOrder(Request $request){
        $user_id = $request->input('authUser')->id;
        $car = Tcar::where('user_id', $user_id)->first();
        if(!$car) {
            return response()->json(['data' => []]);
        }
        return TcarBookingResource::collection($car->bookings()
            ->where('status', TcarBooking::STATUS_ORDER)
            ->withCarType()
            ->with('user', 'driver')
            ->orderBy('created_at', 'DESC')->get());
    }

    public function book(TcarBookingRequest $request){
        try {
            $savedBooking = $this->dispatchSync(new TcarBookingJob($request));
            $booking = TcarBooking::withCarType()
                ->with('user', 'driver')->find($savedBooking->id);
            event(new SearchStartEvent($booking));
            return TcarBookingResource::make($booking);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    public function getOrder(TcarBooking $tcarBooking)
    {
        $booking = TcarBooking::withCarType()
            ->with('user', 'driver')->find($tcarBooking->id);
        return TcarBookingResource::make($booking);
    }

    public function orders(Request $request)
    {
        $orders = TcarBooking::where('driver_id', $request->input('authUser')->id)
            ->withCarType()
            ->with('user', 'driver')
            ->orderBy('created_at', 'DESC')->paginate();
        return TcarBookingResource::collection($orders);
    }

    public function clientOrders(Request $request)
    {
        $orders = TcarBooking::where('user_id', $request->input('authUser')->id)
            ->withCarType()
            ->with('user', 'driver')
            ->orderBy('created_at', 'DESC')->paginate();
        return TcarBookingResource::collection($orders);
    }

    public function changeState(TcarBooking $tcarBooking, TcarBookingStatusRequest $request)
    {

        $last_time = $tcarBooking->last_status_update;
        $last_status = $tcarBooking->status;

        $tcarBooking->status = $request->input('status', $tcarBooking->status);

        $waiting_time = 0;
        if($last_status == TcarBooking::STATUS_ARRIVED && $tcarBooking->status == TcarBooking::STATUS_PROCESSING) {
            $waiting_time = ($tcarBooking->waiting_time ?? 0) + (time() - $last_time);
        }
        if($last_status == TcarBooking::STATUS_PAUSE && ($tcarBooking->status == TcarBooking::STATUS_PROCESSING || $tcarBooking->status == TcarBooking::STATUS_DONE|| $tcarBooking->status == TcarBooking::STATUS_CANCELED)) {
            $waiting_time = ($truckBooking->waiting_time ?? 0) + (time() - $last_time);
        }

        $driver_id = $request->input('driver_id', null);
        $cars = $tcarBooking->cars;

        if($tcarBooking->status == TcarBooking::STATUS_ACCEPTED) {
            if ($tcarBooking->driver_id) {
                return response()->json([
                    'message' => trans('api.booking_accepted')
                ], 403);
            }
            $tcarBooking->cars()->detach();
            $tcarBooking->driver_id = $driver_id;
            event(new StartOrderListUpdatingEvent($cars));
        }

        if($waiting_time > 0) {
            $tcarBooking->price += $tcarBooking->carType->price_per_min * $waiting_time;
        }

        if ($tcarBooking->status == TcarBooking::STATUS_DONE) {
            //Change balance
        }

        $tcarBooking->waiting_time = $waiting_time ?? null;
        $tcarBooking->last_status_update = time();

        $tcarBooking->save();
        $tcarBooking->refresh();

        if($tcarBooking->status == TcarBooking::STATUS_ORDER) {
            $tcarBooking->driver_id = null;
            $tcarBooking->cars()->detach();
            $tcarBooking->findAndAttachCars();
            event(new StartOrderListUpdatingEvent($cars));
        }


        if($tcarBooking->status == TcarBooking::STATUS_NEW) {
            $tcarBooking->driver_id = null;
            $tcarBooking->cars()->detach();
            $tcarBooking->findAndAttachCars();
            event(new SearchStartEvent($tcarBooking));
        }

        event(new TcarOrderStatusEvent($tcarBooking, $tcarBooking->id));
        return TcarBookingResource::make($tcarBooking);
    }

    public function leaveReview(TcarBooking $tcarBooking, TcarBookingReviewRequest $request)
    {
        try {
            $savedBooking = $this->dispatchSync(new TcarBookingReviewJob($request, $tcarBooking));
            $booking = TcarBooking::withCarType()
                ->with('user', 'driver')->find($savedBooking->id);
            return TcarBookingResource::make($booking);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    public function messages(TcarBooking $tcarBooking, Request $request)
    {
        $tcarBooking->messages()
            ->where('user_id', '!=', $request->input('authUser')->id)
            ->update(['read' => 1]);
        return MessageResource::collection($tcarBooking->messages);
    }

    public function sendMessage(TcarBooking $tcarBooking, SendMessageRequest $request)
    {
        $message = new TcarMessage([
            'user_id' => $request->input('authUser')->id,
            'message' => $request->input('message', null),
            'read' => 0
        ]);
        try {
            $savedMessage = $this->dispatchSync(new SendMessageJob($message, $tcarBooking));
            $messageUpdated = TcarMessage::with('user')->find($savedMessage->id);
            return MessageResource::make($messageUpdated);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }
}
