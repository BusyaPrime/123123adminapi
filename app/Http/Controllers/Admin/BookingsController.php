<?php


namespace App\Http\Controllers\Admin;


use App\Domain\Cars\Filter\CarFilter;
use App\Domain\Cars\Models\Car;
use App\Domain\CargoTypes\Models\CargoType;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\CarTypes\Models\CarTypeRate;
use App\Domain\CarTypes\Resources\CarTypeResource;
use App\Domain\CommissionRate\Models\CommissionRate;
use App\Domain\Companies\Models\Company;
use App\Domain\PushNotifications\Jobs\SendPushJob;
use App\Domain\Regions\Models\Region;
use App\Domain\AdminRoles\Models\AdminRole;
use App\Domain\Seasons\Models\Season;
use App\Domain\TruckBookings\Jobs\CalculateBookingPriceJob;
use App\Domain\TruckBookings\Exports\BookingsExport;
use App\Domain\TruckBookings\Exceptions\CompanyNotFoundForPaymentException;
use App\Domain\TruckBookings\Filters\TruckBookingsFilter;
use App\Domain\TruckBookings\Filters\BookingPriceOffersFilter;
use App\Domain\TruckBookings\Jobs\TruckBookingJob;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\TruckBookings\Requests\TruckBookingRequest;
use App\Domain\TruckBookings\Requests\TruckBookingStatusRequest;
use App\Domain\TruckBookings\Resources\TruckBookingResource;
use App\Domain\Users\Jobs\BookingUserJob;
use App\Domain\Users\Models\User;
use App\Domain\Users\Requests\BookingUserRequest;
use App\Events\OrderListUpdatedEvent;
use App\Events\OrderStatusEvent;
use App\Events\SearchStartEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Domain\TruckBookings\Models\BookingPriceOffer;

class BookingsController extends Controller
{
    public function index(Request $request){

        $filter = new TruckBookingsFilter($request);
        $filters = $filter->filters();
        $countInPage = 100;
        $relations = [
            'user.user',
            'driver',
        ];
        $baseQuery = TruckBooking::filter($filter)
            ->withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with($relations)
            ->withCount(['views', 'bookingPriceOffer']);

        if ($filters['date_start'] != '' && $filters['date_end'] != '') {
            $bookings = (clone $baseQuery)
                ->whereBetween('created_at', [date('Y-m-d 00:00:00', strtotime($filters['date_start'] ?? 0)), date('Y-m-d 23:59:59', strtotime($filters['date_end'] ?? 0))])
                ->paginateFilter($countInPage);

        } elseif ($filters['date_start'] != '') {
            $bookings = (clone $baseQuery)
                ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($filters['date_start'] ?? 0)))
                ->paginateFilter($countInPage);
        } elseif ($filters['date_end'] != '') {
            $bookings = (clone $baseQuery)
                ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($filters['date_end'] ?? 0)))
                ->paginateFilter($countInPage);
        } else {
            $bookings = (clone $baseQuery)
                ->paginateFilter($countInPage);
        }

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'carTypes' => CarType::query()
                ->withTranslation()
                ->orderBy('priority')
                ->get(),
            'regions' => Region::query()->orderBy('title')->get(['id', 'title']),
            'cargoTypes' => CargoType::query()
                ->withTranslation()
                ->get()
                ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
                ->values(),
            'companyClients' => Company::query()
                ->where('role', Company::ROLE_COMPANY)
                ->select(['id', 'title'])
                ->orderBy('title')
                ->get(),
            'filters' => $filters,
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
            'Причина отмены',
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
                $booking->commission ?? 0,
                $booking->cancel_reason ? $booking->cancel_reason->reason : '' . "\n".
                $booking->cancel_reason_comment??''
            ];
            $exportData->push($bookingData);
        }

        $export = new BookingsExport($exportData);

        return Excel::download($export, config('app.name').' - bookings '.(date('Y-m-d')).'.xlsx');
    }

    public function show(TruckBooking $booking, Request $request)
    {
        $bookingWithRelations = TruckBooking::withCarType()
        ->withCargoType()
        ->withLoadType()
        ->with('user', 'driver')
        ->findOrFail($booking->id);

        $drivers_nearby = collect([]);
        if($booking->status == TruckBooking::STATUS_ORDER){
            $routes = json_decode($booking->routes, true);
            
            $drivers_nearby = Car::where('car_type_id', $booking->car_type_id)
                ->whereHas('user.profile', function($query) use($booking) {
                    $routes = json_decode($booking->routes, true);
                    $routes = $routes[0];
                    $query->select(\DB::raw("*, ( 3959 * acos( cos( radians('".$routes["lat"]."') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('".$routes["lng"]."') ) + sin( radians('".$routes["lat"]."') ) * sin( radians( lat ) ) ) ) AS distance"))->havingRaw('distance <= '.TruckBooking::NEARBY_DRIVERS_RADIUS);
                })
                ->limit(50)
                ->get();
        }

        $filter = new CarFilter($request);
        $cars = Car::filter($filter)->with('user')->get();
        $admin = auth()->user();
        $permissions = AdminRole::permissionsList();
        

        return view('admin.bookings.show', [
            'booking' => $bookingWithRelations,
            'drivers' => $cars,
            'admin' => $admin,
            'drivers_nearby' => $drivers_nearby,
            'permissions' => $permissions
        ]);
    }

    public function viewsPreview(TruckBooking $booking)
    {
        $views = $booking->views()
            ->latest('created_at')
            ->with('driver.user.profile')
            ->get();

        return view('admin.bookings.partials.views-preview', [
            'views' => $views,
        ]);
    }

    public function priceOffersPreview(TruckBooking $booking)
    {
        $priceOffers = $booking->bookingPriceOffer()
            ->latest('created_at')
            ->with('driver.profile', 'driver.car.carType')
            ->get();

        return view('admin.bookings.partials.price-offers-preview', [
            'priceOffers' => $priceOffers,
        ]);
    }

    public function create()
    {
        $regionSam = Region::find(12);
        $otherRegions = Region::where('id', '!=', 12)->get();
        $otherRegions->push($regionSam);

        return view('admin.bookings.create', [
            'regions' => $otherRegions
        ]);
    }

    public function store(Request $request)
    {
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

        return view('admin.bookings.store', [
            'data' => $request->toArray(),
            'date' => $date,
            'time' => $time,
            'rates' => CarTypeResource::collection($isList ? $carTypes : $carTypesSorted),
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
            if($request->input('instant')) {
                event(new SearchStartEvent($booking));
            }

            $bookingResource = TruckBookingResource::make($booking);
            $data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];

            $cars = $booking->cars;
            foreach ($cars as $car) {
                $request->merge(['user_id' => $car->user_id]);
                $request->merge(['title' => 'Заказ №' . $booking->id]);
                $request->merge(['message' => 'Доступен новый заказ']);

                $this->dispatchSync(new SendPushJob($request, 'drivers', true, $data));
            }

            return redirect()->route('admin.bookings.show', $booking)->with('success', trans('admin.store_success'));
        } catch (CompanyNotFoundForPaymentException $exception) {
            return redirect()->route('admin.bookings.index')->with('danger', $exception->getMessage());
        } catch (\Exception $exception) {
            return redirect()->route('admin.bookings.index')->with('danger', trans('admin.store_failed'));
        }
    }

    public function changeStatus(Request $req, TruckBooking $booking){
        try {
            $status = $req->status;
            $driverNotAttached = 'Заказ не имеет водителя, сначала назначьте водителя';
            $data = ['booking_id' => $booking->id];

            if($status == TruckBooking::STATUS_ORDER) {
                $driver_id = $booking->driver_id;
                $booking->way_in_progress = 0;
                $booking->driver_id = null;
                $booking->last_status_update = null;
                $booking->status = $status;
                $booking->save();

                $status = $booking->status;
                $req->merge([
                    'user_id' => $booking->user_id,
                    'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
                    'message' => ['message' => 'messages.push_messages.status_changed', 'arg' => trans('messages.booking_statuses.' . $status, [], $booking->user->language ?? \App::getLocale())],
                    'notification_type' => 'booking'
                ]);
                $this->dispatchSync(new SendPushJob($req, '', true, $data));
                $req->merge(['user_id' => $driver_id]);
                $this->dispatchSync(new SendPushJob($req, '', true, $data));

                event(new OrderListUpdatedEvent([], 'trucks.all'));
                event(new OrderListUpdatedEvent([], 'all.'.$booking->user_id));
                event(new OrderStatusEvent($booking, $booking->getDriverCar(), '', $booking->id));

                // event(new OrderStatusEvent($booking, $booking->getDriverCar(), $booking->user->username, $booking->id));
                
                return redirect()->route('admin.bookings.show', $booking)->with('success', trans('admin.store_success'));
            } else if($status == TruckBooking::STATUS_ACCEPTED || $status == TruckBooking::STATUS_PROCESSING || $status == TruckBooking::STATUS_ARRIVED || $status == TruckBooking::STATUS_PAUSE || $status == TruckBooking::STATUS_CONFIRMATION){
                if($booking->driver_id != null){
                    $booking->last_status_update = time();
                    $booking->status = $status;
                    $booking->last_status_update = time();
                    $driver_id = $booking->driver_id;

                    event(new OrderStatusEvent($booking, $booking->getDriverCar(), $booking->user->username, $booking->id));

                    $status = $booking->status;
                    $req->merge([
                        'user_id' => $booking->user_id,
                        'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
                        'message' => ['message' => 'messages.push_messages.status_changed', 'arg' => trans('messages.booking_statuses.' . $status, [], $booking->user->language ?? \App::getLocale())],
                        'notification_type' => 'booking'
                    ]);
                    $this->dispatchSync(new SendPushJob($req, '', true, $data));
                    $req->merge(['user_id' => $driver_id]);
                    $this->dispatchSync(new SendPushJob($req, '', true, $data));
                    $booking->save();

                    return redirect()->route('admin.bookings.show', $booking)->with('success', trans('admin.store_success'));
                } else {
                    return redirect()->route('admin.bookings.show', $booking)->with('danger', $driverNotAttached);
                }
            } else {
                return redirect()->route('admin.bookings.show', $booking)->with('danger', 'Неизвестный статус заказа!');
            }
        } catch (\Exception $exception) {
            return redirect()->route('admin.bookings.show', $booking)->with('danger', trans('admin.update_failed'));
        }
    }

    public function changeState(TruckBooking $truckBooking, TruckBookingStatusRequest $request)
    {
        try {

            $driver_id = $request->input('driver_id', null);

//            $waiting_time = 0;

            $truckBooking->status = $request->input('status', $truckBooking->status);

            if ($truckBooking->status == TruckBooking::STATUS_ACCEPTED) {
//            if ($truckBooking->driver_id) {
//                return response()->json([
//                    'message' => trans('api.booking_accepted')
//                ], 403);
//            }
                $truckBooking->cars()->detach();
                $truckBooking->driver_id = $driver_id;
            }


            if ($truckBooking->status == TruckBooking::STATUS_ACCEPTED) {
                $truckBooking->cars()->detach();
                $truckBooking->driver_id = $driver_id;
                $driver = User::find($driver_id);
                if ($driver && isset($driver->profile) && isset($driver->profile->company)) {
                    $truckBooking->company_id = $driver->profile->company->id;
                    if ($driver->profile->company->commission_rate_id) {
                        $commissionRate = CommissionRate::find($driver->profile->company->commission_rate_id);
                        if ($commissionRate) {
                            $truckBooking->commission = ceil($truckBooking->price * $commissionRate->commission / 100);
                        }
                    }
                    $truckBooking->save();
                }
//            event(new StartOrderListUpdatingEvent($cars));
                if (!$truckBooking->driver_id) {
                    if($truckBooking->created_at) {
                        $accepting_time = ceil((time() - $truckBooking->created_at->timestamp) / 60);
                        $truckBooking->accepting_time = $accepting_time ?? null;
                        $truckBooking->save();
                    }
                }
            }

//            $truckBooking->waiting_time = $waiting_time ?? null;
//            $truckBooking->last_status_update = time();

            $truckBooking->save();
            $truckBooking->refresh();

            $booking = TruckBooking::withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')->find($truckBooking->id);
            $phone = null;
            if ($booking->driver) {
                $phone = $booking->driver->user->username;
            }
            event(new OrderStatusEvent($booking, $booking->getDriverCar(), $phone, $truckBooking->id));

            $bookingResource = TruckBookingResource::make($booking);
            $data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];

            // $status = trans('admin.booking_statuses.' . $booking->status);
            $request->merge([
                'user_id' => $booking->user_id,
                'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
                'message' => [
                    'message' => 'messages.push_messages.status_changed',
                    'arg' => trans('messages.booking_statuses.' . $booking->status, [], $booking->user->language ?? \App::getLocale())
                ],
                'notification_type' => 'booking'
            ]);
            $this->dispatchSync(new SendPushJob($request, 'users', false, $data));

            if ($booking->driver_id) {
                $request->merge([
                    'user_id' => $booking->driver_id,
                    'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
                    'message' => 'Вам назначен новый заказ'
                ]);
                $this->dispatchSync(new SendPushJob($request, 'drivers', false, $data));
            }

//            return TruckBookingResource::make($truckBooking);

            return redirect()->route('admin.bookings.show', $truckBooking)->with('success', "Водитель назначен");
        } catch (\Exception $exception) {
            return redirect()->route('admin.bookings.show', $truckBooking)->with('danger', "Не удалось назначить водителя");
        }
    }

    public function cancel(TruckBooking $truckBooking, Request $request)
    {
        try {
            $truckBooking->status = TruckBooking::STATUS_CANCELED;
            $truckBooking->cancel_reason_id = $request->input('cancel_reason_id') == 'null' ? null: $request->input('cancel_reason_id');
            $truckBooking->cancel_reason_comment = $request->input('cancel_reason_comment');
            $truckBooking->save();
                $truckBooking->cars()->detach();
//            event(new StartOrderListUpdatingEvent($cars));
                $truckBooking->save();

                $booking = TruckBooking::withCarType()
                    ->withCargoType()
                    ->withLoadType()
                    ->with('user', 'driver')->find($truckBooking->id);
                $phone = null;
                if ($booking->driver) {
                    $phone = $booking->driver->user->username;
                }

                event(new OrderStatusEvent($booking, $booking->getDriverCar(), $booking->user_id, $truckBooking->id));
                event(new OrderListUpdatedEvent([], 'all.' . $booking->user_id));

                $bookingResource = TruckBookingResource::make($booking);
                $data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];

                $status = $booking->status;
                $request->merge([
                    'user_id' => $booking->user_id,
                    'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
                    'message' => ['message' => 'messages.push_messages.status_changed', 'arg' => trans('messages.booking_statuses.' . $status, [], $booking->user->language ?? \App::getLocale())]
                ]);
                $this->dispatchSync(new SendPushJob($request, '', true, $data));

                if ($booking->driver_id) {
                    $request->merge([
                        'user_id' => $booking->driver_id,
                        'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
                        'message' => ['message' => 'messages.push_messages.status_changed', 'arg' => trans('messages.booking_statuses.' . $status, [], $booking->driver->language ?? \App::getLocale())]
                    ]);
                    $this->dispatchSync(new SendPushJob($request, '', true, $data));
                }
            return redirect()->route('admin.bookings.show', $truckBooking)->with('success', "Заказ отменен");
        } catch (\Exception $exception) {
            return redirect()->route('admin.bookings.show', $truckBooking)->with('danger', "Не удалось отменить заказ");
        }
    }
    
    public function additionalPrice(TruckBooking $truckBooking, Request $request)
    {
        try {
            $additionalPrice = (int)str_replace(' ', '', $request->input('additional_price', 0));
            $additionalPriceReview = $request->input('additional_price_review', '');
            
            if($truckBooking->additional_price != $additionalPrice) {
                $truckBooking->price -= $truckBooking->additional_price;
                $truckBooking->additional_price = $additionalPrice;
                $truckBooking->additional_price_review = $additionalPriceReview;
                $price = $truckBooking->driver_price + $additionalPrice;

                if($truckBooking->payment_type === TruckBooking::PAY_COMPANY) {
                    $truckBooking->price = floor($price * (1 + TruckBooking::BANK_TRANSFER_COEFFICIENT) / 1000) * 1000;
                } else {
                    $truckBooking->price = $price;
                }

                $truckBooking->save();

                $booking = TruckBooking::withCarType()
                    ->withCargoType()
                    ->withLoadType()
                    ->with('user', 'driver')->findOrFail($truckBooking->id);

                $data = [
                    'booking_id' => $booking->id,
                    'booking_info' => [
                        'id' => $booking->id,
                        'status' => $booking->status,
                        'price_changed' => '1'
                    ]
                ];
                $request->merge([
                    'user_id' => $booking->user_id,
                    'title' => trans('messages.push_messages.order', ['arg' => $booking->id], $booking->user->language ?? \App::getLocale()),
                    'message' => trans('messages.push_messages.price_changed', ['sum' => number_format($truckBooking->additional_price, 0, " ", " "), 'argument' => $truckBooking->additional_price_review], $booking->user->language ?? \App::getLocale()),
                    'notification_type' => 'booking'
                ]);
                $this->dispatchSync(new SendPushJob($request, 'users', true, $data));

                if ($booking->driver_id) {
                    $request->merge([
                        'user_id' => $booking->driver_id,
                        'title' => trans('messages.push_messages.order', ['arg' => $booking->id]),
                        'message' => trans('messages.push_messages.price_changed', ['sum' => number_format($truckBooking->additional_price, 0, " ", " "), 'argument' => $truckBooking->additional_price_review], $booking->driver->language ?? \App::getLocale()),
                        'notification_type' => 'booking'
                    ]);
                    $this->dispatchSync(new SendPushJob($request, '', true, $data));
                }
            }

            return redirect()->route('admin.bookings.show', $truckBooking)->with('success', "Сумма добавлена");
        } catch (\Exception $exception) {
            return redirect()->route('admin.bookings.show', $truckBooking)->with('danger', "Не удалось изменить сумму заказа!");
        }
    }

    public function driverBookingOffers(Request $request){
        $filter = new BookingPriceOffersFilter($request);
        $filters = $filter->filters();

        $bookings = TruckBooking::whereHas('bookingPriceOffer')->with('bookingPriceOffer')->with('bookingPriceOffer.driver')->filter($filter)->paginate(30);

        return view('admin.booking-driver-offers.index', [
            'bookings' => $bookings,
            'filters' => $filters
        ]);
    }
    
    public function driverOffer(Request $request, BookingPriceOffer $driverOffer){

        return view('admin.booking-driver-offers.show', [
            'driverOffer' => $driverOffer,
        ]);
    }

    public function showDriverOffer(Request $request, BookingPriceOffer $driverOffer)
    {
        $driverOffer->load('driver.profile', 'driver.car');
        $booking = TruckBooking::with('user', 'driver')->find($driverOffer->booking_id);

        return view('admin.booking-driver-offers.show', [
            'driverOffer' => $driverOffer,
            'booking' => $booking,
        ]);
    }
    
    public function excelImport(){
        
    }

    public function yearlyStatistics(Request $request)
    {
        $dateStart = date('Y-01-01 00:00:00', time());
        $dateEnd = date('Y-12-31 23:59:59', time());
        $paymentTypeFilter = $request->input('payment_type', 'cash');

        $bookings = TruckBooking::whereBetween('created_at', [$dateStart, $dateEnd])
                                ->where('status', TruckBooking::STATUS_DONE)
                                ->where('payment_type', $paymentTypeFilter)
                                ->get();

        if($bookings->count() === 0) {
            return redirect()->back()->with(['error', 'Ничего не найдено!']);
        }

        $exportData = collect();
        $exportData->push([
            'Пользователь',
            'Точка А/Точка Б',
            'Тип и тоннаж',
            'Статус обработки',
            'Дата',
            'Стоимость',
            'Тип оплаты',
        ]);

        $priceSumMonthly = 0;

        for($i = 0; $i < count($bookings); $i++) {
            $booking = $bookings[$i];
            $routes = json_decode($booking->routes ?? '', true);
            $regionFrom = isset($routes[0]) && isset($routes[0]['address']) ? $routes[0]['address'] : 'Адрес не указан';
            $regionTo = isset($routes[1]) && isset($routes[1]['address']) ? $routes[1]['address'] : 'Адрес не указан';

            if($i === 0 || ($bookings[$i - 1]->created_at->month??1) < $booking->created_at->month) {
                $monthName = trans("admin.months.{$booking->created_at->month}");
                $exportData->push([date("01 $monthName Y")]);
                
                if($i > 0) {
                    $exportData->push(['Сумма за месяц: '. number_format($priceSumMonthly, 0, '', ' ')]);
                    $priceSumMonthly = 0;
                }
            }

            $priceSumMonthly += $booking->price;

            $bookingData = [
                ($booking->user->surname ?? '') . ' ' . ($booking->user->name ?? '') . ' ' . ($booking->user->middle_name ?? ''),
                ($regionFrom ?? 'Регион не определен') . ' - ' . $regionTo ?? 'Регион не определен',
                ($booking->cargoType->title ?? 'Тип не указан') . ' / ' . round(($booking->weight ?? 0) / 1000, 3) . ' т.',
                $booking->driver_id ? (($booking->driver->surname ?? '') . ' ' . ($booking->driver->name ?? '') . ' ' . ($booking->driver->middle_name ?? '')) : 'Свободен',
                $booking->created_at ? $booking->created_at->format('d.m.Y') : '--',
                (int)$booking->price ?? 0,
                trans("admin.payment_types.{$booking->payment_type}"),
            ];

            $exportData->push($bookingData);
        }

        $export = new BookingsExport($exportData);

        return Excel::download($export, config('app.name') . ' - bookings ' . (date('Y-m-d')) . '.xlsx');
    }

    public function bookingsByDrivers()
    {
        $drivers = User::whereHas('car')
                    ->with('profile', 'car')
                    ->with(['car.truck_bookings' => function($qb) {
                        $qb->where('status', TruckBooking::STATUS_DONE);
                    }])
                    ->get();

        $exportData = collect();
        $exportData->push([
            'ФИО',
            'Дата регистрации',
            'Количество завершенных заказов',
        ]);

        for ($i = 0; $i < count($drivers); $i++) {
            $driver = $drivers[$i];
            
            $bookingData = [
                $driver->profile->name . ' '. $driver->profile->surname . ' '. $driver->profile->middleName,
                $driver->profile->created_at,
                (string)($driver->car->truck_bookings->count()??0)
            ];

            $exportData->push($bookingData);
        }

        $export = new BookingsExport($exportData);

        return Excel::download($export, config('app.name') . ' - driver-bookings ' . (date('Y-m-d')) . '.xlsx');
    }

    public function editPrice(Request $request, TruckBooking $truckBooking)
    {
        $price = (int)str_replace(' ', '', $request->input('booking_price', 0));

        if($price > 0) {
            if($request->has('type') && $request->input('type') === 'client_price') {
                $truckBooking->price = $price;

                if ($truckBooking->payment_type === TruckBooking::PAY_COMPANY) {
                    $truckBooking->driver_price = floor($price * (1 - TruckBooking::BANK_TRANSFER_COEFFICIENT) / 1000) * 1000;
                } else {
                    $truckBooking->driver_price = $price;
                }
            }
            else if($request->input('type', '') !== 'client_price') {
                $truckBooking->driver_price = $price;

                if ($truckBooking->payment_type === TruckBooking::PAY_COMPANY) {
                    $truckBooking->price = floor($price * (1 + TruckBooking::BANK_TRANSFER_COEFFICIENT) / 1000) * 1000;
                } else {
                    $truckBooking->price = $price;
                }
            }

            $truckBooking->save();
        }

        return redirect()->route('admin.bookings.show', $truckBooking)->with('success', "Цена изменена");
    }
}
