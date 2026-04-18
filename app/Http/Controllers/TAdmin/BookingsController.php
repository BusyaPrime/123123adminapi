<?php

namespace App\Http\Controllers\TAdmin;

use App\Domain\Cars\Filter\CarFilter;
use App\Domain\Cars\Models\Car;
use App\Domain\CargoTypes\Models\CargoType;
use App\Domain\CommissionRate\Models\CommissionRate;
use App\Domain\PushNotifications\Jobs\SendPushJob;
use App\Domain\TruckBookings\Jobs\CalculateBookingPriceJob;
use App\Domain\TruckBookings\Exports\BookingsExport;
use App\Domain\TruckBookings\Exceptions\CompanyNotFoundForPaymentException;
use App\Domain\TruckBookings\Filters\TruckBookingsFilter;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\TruckBookings\Requests\TruckBookingStatusRequest;
use App\Domain\TruckBookings\Resources\TruckBookingResource;
use App\Domain\Users\Models\User;
use App\Domain\Regions\Models\Region;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\CarTypes\Models\CarTypeRate;
use App\Domain\CarTypes\Resources\CarTypeResource;
use App\Domain\Users\Jobs\BookingUserJob;
use App\Domain\TruckBookings\Jobs\TruckBookingJob;
use App\Events\SearchStartEvent;
use App\Domain\Seasons\Models\Season;
use App\Events\OrderStatusEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookingsController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->input('_company');
        
        if($company->user->is_external == 1){
            $users = $company->users()->get();
            $userIds = collect([]);
            $filter = new TruckBookingsFilter($request);
            $filters = $filter->filters();
        
            foreach($users as $u){
                $userIds->add($u->user_id);
            }
            $userIds->add($company->user->id);
            $bookings = TruckBooking::filter($filter)->whereIn('user_id', $userIds);

            if ($filters['date_start'] != '' && $filters['date_end'] != '') {

                $bookings = $bookings
                    ->whereBetween('created_at', [date('Y-m-d 00:00:00', strtotime($filters['date_start']?? 0)), date('Y-m-d 23:59:59', strtotime($filters['date_end']?? 0))])
                    ->withCarType()
                    ->withCargoType()
                    ->withLoadType()
                    ->with('user', 'driver', 'clientCompany')
                    ->paginateFilter();

            } elseif ($filters['date_start'] != '') {
                $bookings = $bookings
                    ->where('created_at', '>=',date('Y-m-d 00:00:00', strtotime($filters['date_start']?? 0)))
                    ->withCarType()
                    ->withCargoType()
                    ->withLoadType()
                    ->with('user', 'driver', 'clientCompany')
                    ->paginateFilter();
            } elseif ($filters['date_end'] != '') {
                $bookings = $bookings
                    ->where('created_at', '<=',date('Y-m-d 23:59:59', strtotime($filters['date_end']?? 0)))
                    ->withCarType()
                    ->withCargoType()
                    ->withLoadType()
                    ->with('user', 'driver', 'clientCompany')
                    ->paginateFilter();
            } else {
                $bookings = $bookings
                    ->withCarType()
                    ->withCargoType()
                    ->withLoadType()
                    ->with('user', 'driver', 'clientCompany')
                    ->paginateFilter();
            }
        } else {
            $request->merge(['company_id' => $company->id]);
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
        }
        

        return view('admin2.bookings.index', [
            'bookings' => $bookings,
            'company' => $company,
            'regions' => Region::query()->orderBy('title')->get(['id', 'title']),
            'cargoTypes' => CargoType::query()
                ->withTranslation()
                ->get()
                ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
                ->values(),
			'user' => $company->user,
            'filters' => $filter->filters(),
			'is_external' => $company->user->is_external,
        ]);
    }
	
	public function export(Request $request)
    {
        $filter = new TruckBookingsFilter($request);
        $filters = $filter->filters();

        if ($filters['date_start'] != '' && $filters['date_end'] != '') {

            $bookings = TruckBooking::filter($filter)
                ->whereBetween('created_at', [date('Y-m-d 00:00:00', strtotime($filters['date_start'] ?? 0)), date('Y-m-d 23:59:59', strtotime($filters['date_end'] ?? 0))])
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->get();

        } elseif ($filters['date_start'] != '') {
            $bookings = TruckBooking::filter($filter)
                ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_start'] ?? 0)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->get();
        } elseif ($filters['date_end'] != '') {
            $bookings = TruckBooking::filter($filter)
                ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_end'] ?? 0)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->get();
        } else {
            $bookings = TruckBooking::filter($filter)
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->get();
        }

        $exportData = collect();
        $exportData->push([
            'ID',
            'Пользователь',
            'Точка А/Точка Б',
            'Тип и тоннаж',
            'Статус обработки',
            'Статус заказа',
            'Дата',
            'Стоимость',
            'Комиссия',
        ]);

        foreach ($bookings as $booking) {
            $routes = json_decode($booking->routes ?? '', true);
            $regionFrom = isset($routes[0]) && isset($routes[0]['address']) ? $routes[0]['address'] : 'Адрес не указан';
            $regionTo = isset($routes[1]) && isset($routes[1]['address']) ? $routes[1]['address'] : 'Адрес не указан';

            $bookingData = [
                $booking->id,
                ($booking->user->surname ?? '').' '.($booking->user->name ?? '').' '.($booking->user->middle_name ?? ''),
                ($regionFrom ?? 'Регион не определен').' - '.$regionTo ?? 'Регион не определен',
                ($booking->cargoType->title ?? 'Тип не указан').' / '.round(($booking->weight ?? 0) / 1000, 3).' т.',
                $booking->driver_id ? (($booking->driver->surname ?? '').' '.($booking->driver->name ?? '').' '.($booking->driver->middle_name ?? '')) : 'Свободен',
                trans('admin.booking_statuses.'.$booking->status),
                $booking->created_at ? $booking->created_at->format('d.m.Y'): '--',
                $booking->price ?? 0,
                $booking->commission ?? 0
            ];
            $exportData->push($bookingData);
        }

        $export = new BookingsExport($exportData);

        return Excel::download($export, config('app.name').' - bookings '.(date('Y-m-d')).'.xlsx');
    }

    public function show(TruckBooking $booking, Request $request)
    {
        $company = $request->input('_company');
        if ($company->user->is_external != 1 && $booking->company_id != $company->id) {
            abort(404);
        }
        $request->merge(['company_id' => $company->id]);
        $filter = new CarFilter($request);
        $cars = Car::filter($filter)->with('user')->paginateFilter();

        $bookingWithRelations = TruckBooking::withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with('user', 'driver', 'clientCompany')
            ->findOrFail($booking->id);

        return view('admin2.bookings.show', [
            'booking' => $bookingWithRelations,
            'drivers' => $cars,
			'is_external' => $company->user->is_external,
        ]);
    }


    public function changeState(TruckBooking $truckBooking, TruckBookingStatusRequest $request)
    {

        $company = $request->input('_company');
        if ($truckBooking->company_id != $company->id) {
            return redirect()->route('admin2.bookings.show', $truckBooking)->with('danger', "Не удалось назначить водителя");
        }

        try {

            $driver_id = $request->input('driver_id', null);
            $user = User::find($driver_id);


            if (!isset($user) || !isset($user->profile) || $user->profile->company_id != $company->id) {
                return redirect()->route('admin2.bookings.show', $truckBooking)->with('danger', "Не удалось назначить водителя");
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
            event(new OrderStatusEvent($booking, $booking->getDriverCar(), $phone, $truckBooking->id));

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
            return redirect()->route('admin2.bookings.show', $truckBooking)->with('danger', "Не удалось назначить водителя");
        }
    }

    public function showUser(TruckBooking $booking, Request $request)
    {
        $company = $request->input('_company');
        if ($booking->company_id != $company->id || (!isset($booking->user) || !isset($booking->user->user))) {
            abort(404);
        }
        return view('admin2.users.show', [
            'user' => $booking->user->user,
            'booking' => $booking,
            'orders' => collect([]),
			'is_external' => $company->user->is_external,
        ]);
    }
	
	public function create(Request $request)
    {
		$company = $request->input('_company');
        $regionSam = Region::find(12);
        $otherRegions = Region::where('id', '!=', 12)->get();
        $otherRegions->push($regionSam);

        return view('admin2.bookings.create', [
            'regions' => $otherRegions,
			'is_external' => $company->user->is_external,
        ]);
    }
	
	public function store(Request $request)
    {
		$company = $request->input('_company');
        $dateTime = date('Y-m-d H:i', strtotime($request->input('date_time', date('Y-m-d H:i'))));
        $dateTimeExploded = explode(' ', $dateTime);
        $date = $dateTimeExploded[0] ?? date('Y-m-d');
        $time = $dateTimeExploded[1] ?? date('H:i');

        $carTypes = CarType::withTranslation()
            ->where('max_weight', '>=', $request->input('weight', 0))
            ->where('dimension_x', '>=', $request->input('dimension_x', 0))
            ->where('dimension_y', '>=', $request->input('dimension_y', 0))
            ->where('dimension_z', '>=', $request->input('dimension_z', 0))
            ->orderBy('priority')->get();
        $regionFromId = $request->input('region_from_id', 1);
        $regionToId = $request->input('region_to_id', 1);
        $isList = $request->input('list');

        $dimensionX = $request->input('dimension_x', 0);
        $dimensionY = $request->input('dimension_y', 0);
        $dimensionZ = $request->input('dimension_z', 0);

        $weight = $request->input('weight', 0);

        $distance = $request->input('distance', '0');
        $time_ratio = 1;
        $season = Season::where('month_start', '<=', date('n', strtotime($date)))
            ->where('month_end', '>=', date('n', strtotime($date)))->first();
        $timeExploded = explode(':', $time);
        $timeSeconds = (($timeExploded[0] ?? 0) * 3600) + (($timeExploded[1] ?? 0) * 60);

        $carTypesSorted = collect([]);
        if (!$isList) {

            foreach ($carTypes as $carType) {
                $rate = CarTypeRate::where('car_type_id', $carType->id)
                    ->where('region_from_id', $regionFromId)
                    ->where('region_to_id', $regionToId)
                    ->where('season_id', $season->id ?? 0)->first();
                if ($rate) {

                    $timeRatios = json_decode($rate->time_ratio, true);
                    $pricesDistance = json_decode($rate->prices_distance, true);

                    $lastRateRation = 0;
                    $rateRation = 0;
                    foreach ($pricesDistance as $distancePrice) {
                        if ($distance <= $distancePrice['distance']) {
                            $rateRation = $distancePrice['sum'];
                            break;
                        }
                        $lastRateRation = $distancePrice['sum'];
                    }
                    if($rateRation <= 0) {
                        $rateRation = $lastRateRation;
                    }

                    $volumeFullWeight = 0;
                    if ($rate->divider > 0) {
                        $volumeFullWeight = (($carType->dimension_x * 100) * ($carType->dimension_y * 100) * ($carType->dimension_z * 100)) / $rate->divider;
                    }
                    $fullWeight = $volumeFullWeight > $weight ? $volumeFullWeight : $weight;

                    $volume = $dimensionX * $dimensionY * $dimensionZ;
                    $carTypeVolume = $carType->dimension_x * $carType->dimension_y * $carType->dimension_z;

                    $calculatedPrice = ($distance * $fullWeight * $rateRation) + $rate->min_price;
                    $calculatedNotFullPrice = $calculatedPrice;

                    $volumeWeight = 0;
                    if ($rate->divider > 0) {
                        $volumeWeight = (($dimensionX * 100) * ($dimensionY * 100) * ($dimensionZ * 100)) / $rate->divider;
                    }
                    $notFullWeight = $volumeWeight > $weight ? $volumeWeight : $weight;

                    $volumePercentage = $carTypeVolume > 0 ? ($volume * 100 / $carTypeVolume) : 100;
                    $weightPercentage = $carType->max_weight > 0 ? ($notFullWeight * 100 / $carType->max_weight) : 100;

                    if ($volumePercentage <= $rate->not_full_min_value && $weightPercentage <= $rate->not_full_min_value) {
                        if ($rate->not_full_ratio > 0) {
                            $carType->notFullEnabled = true;
                            $calculatedNotFullPrice = (($distance * $notFullWeight * $rateRation) + $rate->not_full_min_price) * $rate->not_full_ratio;
                        }
                    }

                    foreach ($timeRatios as $ratio) {
                        $startExploded = explode(':', $ratio['start']);
                        $endExploded = explode(':', $ratio['end']);
                        $startSeconds = (($startExploded[0] ?? 0) * 3600) + (($startExploded[1] ?? 0) * 60);
                        $endSeconds = (($endExploded[0] ?? 0) * 3600) + (($endExploded[1] ?? 0) * 60);
                        if ($timeSeconds >= $startSeconds && $timeSeconds <= $endSeconds) {
                            $time_ratio = $ratio['ratio'];
                        }
                    }

//                    $calculatedPrice = $calculatedPriceDistance > $calculatedPrice? $calculatedPriceDistance: $calculatedPrice;

                    if ($calculatedPrice > 0) {
                        $carType->calculatedPrice = round($calculatedPrice * $time_ratio);
                        $carType->calculatedPriceNotFull = round($calculatedNotFullPrice * $time_ratio);
                        $carTypesSorted->add($carType);
                    }
                }
//            $carType->setPrice($request->input('distance', 0), $request->input('region_id', null));
            }
        }

        return view('admin2.bookings.store', [
            'data' => $request->toArray(),
            'date' => $date,
            'time' => $time,
            'rates' => CarTypeResource::collection($isList ? $carTypes : $carTypesSorted),
			'company_id' => $company->id,
			'is_external' => $company->user->is_external,
        ]);
    }
	
	public function book(BookingUserRequest $requestUser, TruckBookingRequest $request)
    {
        try {
            $request->merge(['instant' => 0]);

            $user = $this->dispatchSync(new BookingUserJob($requestUser));
            $request->merge(['authUser' => $user]);
            $carType = CarType::findOrFail($request->input('car_type_id'));
            $carType = $this->dispatchSync(new CalculateBookingPriceJob($request, $carType));
            $savedBooking = $this->dispatchSync(new TruckBookingJob($request, $carType));
            $booking = TruckBooking::withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')->find($savedBooking->id);
            if ($request->input('instant')) {
                event(new SearchStartEvent($booking));
            }

            $bookingResource = TruckBookingResource::make($booking);
            $data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];

            $cars = $booking->cars;
            foreach ($cars as $car) {
                $request->merge(['user_id' => $car->user_id]);
                $request->merge(['title' => 'Заказ №' . $booking->id]);
                $request->merge(['message' => 'Доступен новый заказ']);
                $this->dispatchSync(new SendPushJob($request, 'drivers', false, $data));
            }

            return redirect()->route('admin.bookings.show', $booking)->with('success', trans('admin.store_success'));
        } catch (CompanyNotFoundForPaymentException $exception) {
            return redirect()->route('admin.bookings.index')->with('danger', $exception->getMessage());
        } catch (\Exception $exception) {
            return redirect()->route('admin.bookings.index')->with('danger', trans('admin.store_failed'));
        }
    }

    public function additionalPrice(TruckBooking $truckBooking, Request $request)
    {
        try {
            $additionalPrice = (int)str_replace(' ', '', $request->input('additional_price', 0));
            $additionalPriceReview = $request->input('additional_price_review', '');
            
            if($truckBooking->additional_price != $additionalPrice){
                $truckBooking->price -= $truckBooking->additional_price;
                $truckBooking->additional_price = $additionalPrice;
                $truckBooking->price += $additionalPrice;
                $truckBooking->additional_price_review = $additionalPriceReview;
                $truckBooking->save();

                $booking = TruckBooking::withCarType()
                    ->withCargoType()
                    ->withLoadType()
                    ->with('user', 'driver')->find($truckBooking->id);

                $bookingResource = TruckBookingResource::make($booking);
                $data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];

                $status = trans('admin.booking_statuses.' . $booking->status);
                $request->merge(['user_id' => $booking->user_id]);
                $request->merge(['title' => trans('messages.push_messages.order', ['arg' => $booking->id])]);
                $request->merge(['message' => trans('messages.push_messages.price_changed').number_format($truckBooking->additional_price ,0," "," ").' сум. '.trans('messages.push_messages.cause').$truckBooking->additional_price_review ]);
                $this->dispatchSync(new SendPushJob($request, 'users', false, $data));

                if ($booking->driver_id) {
                    $request->merge(['user_id' => $booking->driver_id]);
                    $request->merge(['title' => trans('messages.push_messages.order', ['arg' => $booking->id])]);
                    $request->merge(['message' => trans('messages.push_messages.price_changed').number_format($truckBooking->additional_price ,0," "," ").' сум. '.trans('messages.push_messages.cause').$truckBooking->additional_price_review ]);
                    $this->dispatchSync(new SendPushJob($request, 'drivers', false, $data));
                }
            }

            return redirect()->route('admin2.bookings.show', $truckBooking)->with('success', "Сумма добавлена");
        } catch (\Exception $exception) {

            return redirect()->route('admin2.bookings.show', $truckBooking)->with('danger', "Не удалось изменить сумму заказа!");
        }
    }
	
}
