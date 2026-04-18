<?php

namespace App\Http\Controllers\TAdmin;

use App\Domain\Cars\Filter\CarFilter;
use App\Domain\Cars\Models\Car;
use App\Domain\CargoTypes\Models\CargoType;
use App\Domain\CommissionRate\Models\CommissionRate;
use App\Domain\PushNotifications\Jobs\SendPushJob;
use App\Domain\TruckBookings\Filters\TruckBookingsFilter;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\TruckBookings\Requests\TruckBookingStatusRequest;
use App\Domain\TruckBookings\Resources\TruckBookingResource;
use App\Domain\Regions\Models\Region;
use App\Domain\Users\Models\User;
use App\Events\OrderStatusEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingsAvailableController extends Controller
{
    public function index(Request $request)
    {
        $request->merge(['status' => 'free']);
		$company = $request->input('_company');
        $filter = new TruckBookingsFilter($request);
        $filters = $filter->filters();

        if ($filters['date_start'] != '' && $filters['date_end'] != '') {

            $bookings = TruckBooking::filter($filter)
                ->whereBetween('created_at', [date('Y-m-d 00:00:00', strtotime($filters['date_start']?? 0)), date('Y-m-d 23:59:59', strtotime($filters['date_end']?? 0))])
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver', 'clientCompany')
                ->paginateFilter();

        } elseif ($filters['date_start'] != '') {
            $bookings = TruckBooking::filter($filter)
                ->where('created_at', '>=',date('Y-m-d 00:00:00', strtotime($filters['date_start']?? 0)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver', 'clientCompany')
                ->paginateFilter();
        } elseif ($filters['date_end'] != '') {
            $bookings = TruckBooking::filter($filter)
                ->where('created_at', '<=',date('Y-m-d 23:59:59', strtotime($filters['date_end']?? 0)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver', 'clientCompany')
                ->paginateFilter();
        } else {
            $bookings = TruckBooking::filter($filter)
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver', 'clientCompany')
                ->paginateFilter();
        }



        return view('admin2.bookings-available.index', [
            'bookings' => $bookings,
            'regions' => Region::query()->orderBy('title')->get(['id', 'title']),
            'cargoTypes' => CargoType::query()
                ->withTranslation()
                ->get()
                ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
                ->values(),
            'filters' => $filter->filters(),
			'is_external' => $company->user->is_external,
        ]);
    }

    public function show(TruckBooking $booking, Request $request)
    {
        if ($booking->company_id || $booking->driver_id) {
            return redirect()->route('admin2.bookings-available.index')->with('warning', "Заказ более не доступен");
        }

        $company = $request->input('_company');
        $request->merge(['company_id' => $company->id]);
        $filter = new CarFilter($request);
        $cars = Car::filter($filter)->with('user')->paginateFilter();

        $bookingWithRelations = TruckBooking::withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with('user', 'driver', 'clientCompany')
            ->findOrFail($booking->id);

        return view('admin2.bookings-available.show', [
            'booking' => $bookingWithRelations,
            'drivers' => $cars,
        ]);
    }


    public function changeState(TruckBooking $truckBooking, TruckBookingStatusRequest $request)
    {

        $company = $request->input('_company');
        if ($truckBooking->company_id || $truckBooking->driver_id) {
            return redirect()->route('admin2.bookings-available.index')->with('warning', "Заказ более не доступен");
        }

        try {
            $driver_id = $request->input('driver_id', null);
            $user = User::find($driver_id);


            if (!isset($user) || !isset($user->profile) || $user->profile->company_id != $company->id) {
                return redirect()->route('admin2.bookings-available.show', $truckBooking)->with('danger', "Не удалось назначить водителя");
            }


            $truckBooking->status = $request->input('status', $truckBooking->status);
            if ($truckBooking->status == TruckBooking::STATUS_ACCEPTED) {
                $truckBooking->cars()->detach();
                $truckBooking->driver_id = $driver_id;
                $truckBooking->company_id = $company->id;
                $commissionRate = CommissionRate::find($company->commission_rate_id ?? 0);
                if($commissionRate) {
                    $truckBooking->commission = ceil($truckBooking->price * $commissionRate->commission / 100);
                }
                $truckBooking->save();

//            event(new StartOrderListUpdatingEvent($cars));

                if($truckBooking->created_at) {
                    $accepting_time = ceil((time() - $truckBooking->created_at->timestamp) / 60);
                    $truckBooking->accepting_time = $accepting_time ?? null;
                    $truckBooking->save();
                }
            }

            $truckBooking->save();
            $truckBooking->refresh();

            $booking = TruckBooking::withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')->find($truckBooking->id);
            $phone = null;
            if($booking->driver) {
                $phone = $booking->driver->user->username;
            }
//            event(new OrderStatusEvent($booking, $booking->getDriverCar(), $phone, $truckBooking->id));

            $bookingResource = TruckBookingResource::make($booking);
            $data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];

            $status = trans('admin.booking_statuses.'.$booking->status);
            $request->merge(['user_id' => $booking->user_id]);
            $request->merge(['title' => trans('admin.push_messages.order').' №'.$booking->id]);
            $request->merge(['message' => trans('admin.push_messages.status_changed').' "'.$status.'"']);
            $this->dispatchSync(new SendPushJob($request, 'users', false, $data));

            if ($booking->driver_id) {
                $request->merge(['user_id' => $booking->driver_id]);
                $request->merge(['title' => 'Заказ №'.$booking->id]);
                $request->merge(['message' => 'Вам назначен новый заказ']);
                $this->dispatchSync(new SendPushJob($request, 'drivers', false, $data));
            }

            return redirect()->route('admin2.bookings.show', $truckBooking)->with('success', "Водитель назначен");
        } catch (\Exception $exception) {
            return redirect()->route('admin2.bookings-available.show', $truckBooking)->with('danger', "Не удалось назначить водителя");
        }
    }
}
