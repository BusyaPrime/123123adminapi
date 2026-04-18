<?php

namespace App\Http\Controllers\Admin;

use App\Domain\CancelReasons\Models\CancelReason;
use App\Domain\CargoTypes\Models\CargoType;
use App\Domain\Cars\Models\Car;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\Companies\Models\Company;
use App\Domain\Regions\Models\Region;
use App\Domain\TruckBookings\Exports\BookingsExport;
use App\Domain\TruckBookings\Exports\MultiSheetExport;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Exports\UsersExport;
use App\Domain\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $preset = $request->input('preset', 'week');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        if ($preset == 'week') {
            $dateStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'month') {
            $dateStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'year') {
            $dateStart = date('Y-m-d 00:00:00', now()->subYear()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'all') {
            $dateStart = null;
            $dateEnd = null;
        }

        $driversRatings = [
            '5' => 0,
            '4.99 - 4.0' => 0,
            '3.99 - 3.0' => 0,
            '2.99 - 2.0' => 0,
            '1.99 - 1.0' => 0,
            '0.99 - 0.01' => 0,
            'Нет оценки' => 0,
        ];

        $lastSeenAt = [
            'Неделя' => 0,
            'Месяц' => 0,
            '3 Месяца' => 0
        ];

        $carTypes = CarType::whereHas('truck_bookings', function ($q) {
            $q->whereNotNull('accepting_time');
        })->limit(5)->get();

        $cancelReasons = CancelReason::all();

        $cancelReasonsCount = [
            'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')->count(),
        ];
        foreach ($cancelReasons as $cancelReason) {
            $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)->count();
        }

        $year = date('Y', $dateEnd ? strtotime($dateEnd) : time());

        $bookingsSumChart = $this->year('sums', $year);
        $bookingsCommissionChart = $this->year('commissions', $year);

        $bookingsCount = TruckBooking::count();
        $bookingsFullCount = TruckBooking::where('not_full', 0)->count();
        $bookingsNotFullCount = TruckBooking::where('not_full', 1)->count();
        $bookingsSum = TruckBooking::where('status', 'done')->sum('price');
        $bookingsCommission = TruckBooking::where('status', 'done')->sum('commission');
        $bookingsCanceledCount = TruckBooking::where('status', 'canceled')->count();
        $usersCount = User::whereNotIn('role', ['admin', 'merchant'])->count();
        $driversCount = User::whereHas('car')->count();


        $bookingsSumTotal = TruckBooking::where('status', 'done')->sum('price');
        $bookingsSumYearTotal = TruckBooking::where('status', 'done')
            ->whereBetween('created_at', [
                date('Y-m-d 00:00:00', now()->subYear()->timestamp),
                date('Y-m-d 23:59:59', now()->timestamp),
            ])
            ->sum('price');
        $bookingsSumMonthTotal = TruckBooking::where('status', 'done')
            ->whereBetween('created_at', [
                date('Y-m-d 00:00:00', now()->subMonth()->timestamp),
                date('Y-m-d 23:59:59', now()->timestamp),
            ])
            ->sum('price');
        $bookingsSumWeekTotal = TruckBooking::where('status', 'done')
            ->whereBetween('created_at', [
                date('Y-m-d 00:00:00', now()->subWeek()->timestamp),
                date('Y-m-d 23:59:59', now()->timestamp),
            ])
            ->sum('price');
        $bookingsSumDayTotal = TruckBooking::where('status', 'done')
            ->whereBetween('created_at', [
                date('Y-m-d 00:00:00', now()->timestamp),
                date('Y-m-d 23:59:59', now()->timestamp),
            ])
            ->sum('price');

        $usersTotalCount = User::whereNotIn('role', ['admin', 'merchant'])->count();
        $driversTotalCount = User::whereHas('car')->count();

        foreach ($driversRatings as $r => $rating) {
            $driversRatings[$r] = User::whereHas('car')
                ->whereHas('profile', function ($query) use ($r) {
                    if ($r == '5') {
                        $query->where('rating', $r);
                    } elseif ($r == '4.99 - 4.0') {
                        $query->whereBetween('rating', [4.0, 4.99]);
                    } elseif ($r == '3.99 - 3.0') {
                        $query->whereBetween('rating', [3.0, 3.99]);
                    } elseif ($r == '2.99 - 2.0') {
                        $query->whereBetween('rating', [2.0, 2.99]);
                    } elseif ($r == '1.99 - 1.0') {
                        $query->whereBetween('rating', [1.0, 1.99]);
                    } elseif ($r == '0.99 - 0.01') {
                        $query->whereBetween('rating', [0.01, 0.99]);
                    } elseif ($r == 'Нет оценки') {
                        $query->whereNull('rating')->orWhere('rating', '')->orWhere('rating', 0);
                    }
                })
                ->count();
        }

        foreach ($lastSeenAt as $r => $lastSeen) {
            $query = User::whereNotIn('role', ['admin', 'merchant']);

            if ($r == 'Неделя') {
                $dStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
                $dEnd = date('Y-m-d 23:59:59');
                $query->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime($dStart)),
                    date('Y-m-d 23:59:59', strtotime($dEnd)),
                ]);
            } elseif ($r == 'Месяц') {
                $dStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
                $dEnd = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
                $query->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime($dStart)),
                    date('Y-m-d 00:00:00', strtotime($dEnd)),
                ]);
            } elseif ($r == '3 Месяца') {
                $dStart = date('Y-m-d 00:00:00', now()->subMonths(3)->timestamp);
                $dEnd = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
                $query->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime($dStart)),
                    date('Y-m-d 00:00:00', strtotime($dEnd)),
                ]);
            }

            $lastSeenAt[$r] = $query->count();
        }

        $acceptingTime = [];
        $loadingTime = [];
        $unloadingTime = [];
        foreach ($carTypes as $carType) {
            $acceptingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                ->where('car_type_id', $carType->id)->avg('accepting_time');
            $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
            $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
        }

        $companiesCount = Company::count();
        $popularDirections = DB::select("
        select region_from_id, region_to_id, count(id) as cnt from truck_bookings
        group by region_from_id, region_to_id
        order by cnt desc limit 5");

        $userByRegionCount = DB::select("
        select region_id, count(id) as cnt from user_profiles
        group by region_id
        order by cnt desc limit 5");
        $popularRegionFrom = DB::select("
        select region_from_id, count(id) as cnt from truck_bookings
        group by region_from_id
        order by cnt desc limit 5");
        $popularRegionTo = DB::select("
        select  region_to_id, count(id) as cnt from truck_bookings
        group by  region_to_id
        order by cnt desc limit 5");
        $popularCarTypes = DB::select("
        select car_type_id, count(id) as cnt from truck_bookings
        group by car_type_id
        order by cnt desc limit 5");
        $popularCargoTypes = DB::select("
        select cargo_type_id, count(id) as cnt from truck_bookings
        group by cargo_type_id
        order by cnt desc limit 5");
        if ($dateStart && $dateEnd) {

            $cancelReasonsCount = [
                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
                    ->whereBetween('created_at', [
                        date('Y-m-d 00:00:00', strtotime($dateStart)),
                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
                    ])->count(),
            ];
            foreach ($cancelReasons as $cancelReason) {
                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])->count();
            }

            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateStart, $dateEnd) {
                $q->whereNotNull('accepting_time')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ]);
            })->limit(5)->get();
            $acceptingTime = [];
            $loadingTime = [];
            $unloadingTime = [];
            foreach ($carTypes as $carType) {
                $acceptingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->whereBetween('created_at', [
                        date('Y-m-d 00:00:00', strtotime($dateStart)),
                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
                    ])
                    ->where('car_type_id', $carType->id)->avg('accepting_time');

                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->whereBetween('created_at', [
                        date('Y-m-d 00:00:00', strtotime($dateStart)),
                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
                    ])
                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->whereBetween('created_at', [
                        date('Y-m-d 00:00:00', strtotime($dateStart)),
                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
                    ])
                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
            }

            $bookingsCount = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->count();
            $bookingsFullCount = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->where('not_full', 0)->count();
            $bookingsNotFullCount = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->where('not_full', 1)->count();
            $bookingsSum = TruckBooking::where('status', 'done')->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->sum('price');
            $bookingsCommission = TruckBooking::where('status', 'done')->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->sum('commission');
            $bookingsCanceledCount = TruckBooking::where('status', 'canceled')
                ->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])->count();
            $usersCount = User::whereNotIn('role', ['admin', 'merchant'])
                ->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])->count();
            $driversCount = User::whereHas('car')
                ->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])->count();
            $companiesCount = Company::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->count();
            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id, region_to_id
                order by cnt desc limit 5");
            $userByRegionCount = DB::select("
                select region_id, count(id) as cnt from user_profiles
                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_id
                order by cnt desc limit 5");
            $popularRegionFrom = DB::select("
                select region_from_id , count(id) as cnt from truck_bookings
                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id
                order by cnt desc limit 5");
            $popularRegionTo = DB::select("
                select  region_to_id, count(id) as cnt from truck_bookings
                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by  region_to_id
                order by cnt desc limit 5");
            $popularCarTypes = DB::select("
                select car_type_id, count(id) as cnt from truck_bookings
                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by car_type_id
                order by cnt desc limit 5");
            $popularCargoTypes = DB::select("
                select cargo_type_id, count(id) as cnt from truck_bookings
                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by cargo_type_id
                order by cnt desc limit 5");
        } elseif ($dateStart) {
            $cancelReasonsCount = [
                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count(),
            ];
            foreach ($cancelReasons as $cancelReason) {
                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            }


            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateStart) {
                $q->whereNotNull('accepting_time')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)));
            })->limit(5)->get();
            $acceptingTime = [];
            $loadingTime = [];
            $unloadingTime = [];
            foreach ($carTypes as $carType) {
                $acceptingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                    ->where('car_type_id', $carType->id)->avg('accepting_time');

                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
            }

            $bookingsCount = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            $bookingsFullCount = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                ->where('not_full', 0)->count();
            $bookingsNotFullCount = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                ->where('not_full', 1)->count();
            $bookingsSum = TruckBooking::where('status', 'done')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->sum('price');
            $bookingsCommission = TruckBooking::where('status', 'done')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->sum('commission');
            $bookingsCanceledCount = TruckBooking::where('status', 'canceled')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            $usersCount = User::whereNotIn('role', ['admin', 'merchant'])->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            $driversCount = User::whereHas('car')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            $companiesCount = Company::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by region_from_id, region_to_id
                order by cnt desc limit 5");

            $userByRegionCount = DB::select("
                select region_id, count(id) as cnt from user_profiles
                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by region_id
                order by cnt desc limit 5");

            $popularRegionFrom = DB::select("
                select region_from_id,  count(id) as cnt from truck_bookings
                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by region_from_id
                order by cnt desc limit 5");
            $popularRegionTo = DB::select("
                select  region_to_id, count(id) as cnt from truck_bookings
                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by  region_to_id
                order by cnt desc limit 5");
            $popularCarTypes = DB::select("
                select car_type_id, count(id) as cnt from truck_bookings
                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by car_type_id
                order by cnt desc limit 5");
            $popularCargoTypes = DB::select("
                select cargo_type_id, count(id) as cnt from truck_bookings
                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by cargo_type_id
                order by cnt desc limit 5");
        } elseif ($dateEnd) {
            $cancelReasonsCount = [
                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count(),
            ];
            foreach ($cancelReasons as $cancelReason) {
                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            }

            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateEnd) {
                $q->whereNotNull('accepting_time')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)));
            })->limit(5)->get();
            $acceptingTime = [];
            $loadingTime = [];
            $unloadingTime = [];
            foreach ($carTypes as $carType) {
                $acceptingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                    ->where('car_type_id', $carType->id)->avg('accepting_time');

                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
            }

            $bookingsCount = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            $bookingsFullCount = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                ->where('not_full', 0)->count();
            $bookingsNotFullCount = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                ->where('not_full', 1)->count();
            $bookingsSum = TruckBooking::where('status', 'done')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->sum('price');
            $bookingsCommission = TruckBooking::where('status', 'done')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->sum('commission');
            $bookingsCanceledCount = TruckBooking::where('status', 'canceled')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            $usersCount = User::whereNotIn('role', ['admin', 'merchant'])->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            $driversCount = User::whereHas('car')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            $companiesCount = Company::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id, region_to_id
                order by cnt desc limit 5");

            $userByRegionCount = DB::select("
                select region_id, count(id) as cnt from user_profiles
                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_id
                order by cnt desc limit 5");

            $popularRegionFrom = DB::select("
                select region_from_id , count(id) as cnt from truck_bookings
                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id
                order by cnt desc limit 5");
            $popularRegionTo = DB::select("
                select  region_to_id, count(id) as cnt from truck_bookings
                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by  region_to_id
                order by cnt desc limit 5");
            $popularCarTypes = DB::select("
                select car_type_id, count(id) as cnt from truck_bookings
                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by car_type_id
                order by cnt desc limit 5");
            $popularCargoTypes = DB::select("
                select cargo_type_id, count(id) as cnt from truck_bookings
                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by cargo_type_id
                order by cnt desc limit 5");
        }

        return view('admin.statistics.index', [
            'preset' => $preset,
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'bookings_count' => $bookingsCount,
            'users_count' => $usersCount,
            'drivers_count' => $driversCount,
            'companies_count' => $companiesCount,
            'bookings_canceled_count' => $bookingsCanceledCount,
            'bookings_sum' => $bookingsSum,
            'bookings_commission' => $bookingsCommission,
            'popular_directions' => collect($popularDirections),
            'user_by_region_count' => collect($userByRegionCount),
            'popular_car_types' => collect($popularCarTypes),
            'popular_cargo_types' => collect($popularCargoTypes),
            'popular_regions_from' => collect($popularRegionFrom),
            'popular_regions_to' => collect($popularRegionTo),
            'bookings_full_count' => $bookingsFullCount,
            'bookings_not_full_count' => $bookingsNotFullCount,
            'bookings_sum_chart' => $bookingsSumChart,
            'bookings_commission_chart' => $bookingsCommissionChart,
            'drivers_ratings' => collect($driversRatings),
            'drivers_total_count' => $driversTotalCount,
            'users_total_count' => $usersTotalCount,
            'last_seen_at' => collect($lastSeenAt),
            'accepting_time' => collect($acceptingTime),
            'loading_time' => collect($loadingTime),
            'unloading_time' => collect($unloadingTime),
            'cancel_reason_count' => collect($cancelReasonsCount),
            'bookings_sum_total' => $bookingsSumTotal,
            'bookings_sum_year_total' => $bookingsSumYearTotal,
            'bookings_sum_month_total' => $bookingsSumMonthTotal,
            'bookings_sum_week_total' => $bookingsSumWeekTotal,
            'bookings_sum_day_total' => $bookingsSumDayTotal,
            'year' => $year,
        ]);
    }

    public function month($model, $month)
    {
        $date = $month ?? now()->format('Y-m');
        $carbonDate = Date::make($date . '-01');
        $daysInMonth = $carbonDate->daysInMonth;
        $year = $carbonDate->format('Y');
        $month = $carbonDate->format('m');
        $pageTitle = '';
        $countByDate = [];
        if ($model == 'bookings') {
            $pageTitle = 'Заказы';
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $countByDate[$i] = TruckBooking::whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                ])->count();
            }
        }
        if ($model == 'cancels') {
            $pageTitle = 'Отмены';
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $countByDate[$i] = TruckBooking::where('status', 'canceled')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                ])->count();
            }
        }
        if ($model == 'sums') {
            $pageTitle = 'Оборот';
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $countByDate[$i] = TruckBooking::where('status', 'done')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                ])->sum('price');
            }
        }
        if ($model == 'commissions') {
            $pageTitle = 'Отчисления';
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $countByDate[$i] = TruckBooking::where('status', 'done')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                ])->sum('commission');
            }
        }
        if ($model == 'companies') {
            $pageTitle = 'Компании';
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $countByDate[$i] = Company::whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                ])->count();
            }
        }
        if ($model == 'users') {
            $pageTitle = 'Пользователи';
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $countByDate[$i] = User::whereNotIn('role', ['admin', 'merchant'])->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                ])->count();
            }
        }
        if ($model == 'drivers') {
            $pageTitle = 'Водители';
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $countByDate[$i] = User::whereHas('car')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i))),
                ])->count();
            }
        }

        return [
            'date' => $date,
            'model' => $model,
            'days_in_month' => $daysInMonth,
            'page_title' => $pageTitle,
            'count_by_date' => $countByDate,
        ];
    }

    protected function year($model, $year)
    {
        $date = $year ?? now()->format('Y');
        $pageTitle = '';
        $countByDate = [];

        for ($i = 1; $i <= 12; $i++) {
            $carbonDate = Date::make($date . '-' . ($i < 10 ? '0' . $i : $i) . '-01');
            $year = $carbonDate->format('Y');
            $month = $carbonDate->format('m');
            $daysInMonth = $carbonDate->daysInMonth;

            if ($model == 'bookings') {
                $pageTitle = 'Заказы';
                $countByDate[$i] = TruckBooking::whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
                ])->count();
            }

            if ($model == 'cancels') {
                $pageTitle = 'Отмены';
                $countByDate[$i] = TruckBooking::where('status', 'canceled')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
                ])->count();
            }

            if ($model == 'sums') {
                $pageTitle = 'Оборот';
                $countByDate[$i] = TruckBooking::where('status', 'done')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
                ])->sum('price');
            }

            if ($model == 'commissions') {
                $pageTitle = 'Отчисления';
                $countByDate[$i] = TruckBooking::where('status', 'done')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
                ])->sum('commission');
            }

            if ($model == 'companies') {
                $pageTitle = 'Компании';
                $countByDate[$i] = Company::whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
                ])->count();
            }

            if ($model == 'users') {
                $pageTitle = 'Пользователи';
                $countByDate[$i] = User::whereNotIn('role', ['admin', 'merchant'])->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
                ])->count();
            }

            if ($model == 'drivers') {
                $pageTitle = 'Водители';
                $countByDate[$i] = User::whereHas('car')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
                ])->count();
            }

        }

        return [
            'date' => $date,
            'model' => $model,
//            'page_title' => $pageTitle,
            'count_by_date' => $countByDate,
        ];
    }

    public function bookingsYearDetails(Request $request)
    {
        $year = $request->input('date', now()->format('Y'));

        $bookingsAwaiting = TruckBooking::whereIn('status', ['order', 'new'])
            ->whereBetween('created_at', [$year . '-01-01 00:00:00', $year . '-12-31 23:59:59'])->count();
        $bookingsInProgress = TruckBooking::whereIn('status', ['waiting', 'accepted', 'arrived', 'processing', 'pause'])
            ->whereBetween('created_at', [$year . '-01-01 00:00:00', $year . '-12-31 23:59:59'])->count();
        $bookingsDone = TruckBooking::where('status', 'done')
            ->whereBetween('created_at', [$year . '-01-01 00:00:00', $year . '-12-31 23:59:59'])->count();
        $bookingsCancels = TruckBooking::where('status', 'canceled')
            ->whereBetween('created_at', [$year . '-01-01 00:00:00', $year . '-12-31 23:59:59'])->count();

        $bookingsCount = $this->year('bookings', $year);
        $bookingsCancelsCount = $this->year('cancels', $year);

        $userPermissions = [];
        $user = $request->user();
        if ($user) {
            $adminRole = $user->adminRole;
            if ($adminRole) {
                $userPermissions = json_decode($user->adminRole->permissions, true);
            }
        }

        return view('admin.statistics.bookings.year', [
            'bookings_count' => $bookingsCount,
            'date' => $year,
            'bookings_cancel_count' => $bookingsCancelsCount,
            'bookings_awaiting' => $bookingsAwaiting,
            'bookings_in_progress' => $bookingsInProgress,
            'bookings_done' => $bookingsDone,
            'bookings_cancels' => $bookingsCancels,
            'user_permissions' => $userPermissions,
        ]);
    }

    public function bookingsMonthDetails(Request $request)
    {
        $month = $request->input('date', now()->format('Y-m'));

        $bookingsCount = $this->month('bookings', $month);
        $bookingsCancelsCount = $this->month('cancels', $month);

        $bookingsAwaiting = TruckBooking::whereIn('status', ['order', 'new'])
            ->whereBetween('created_at', [$month . '-01 00:00:00', $month . '-' . $bookingsCount['days_in_month'] . ' 23:59:59'])->count();
        $bookingsInProgress = TruckBooking::whereIn('status', ['waiting', 'accepted', 'arrived', 'processing', 'pause'])
            ->whereBetween('created_at', [$month . '-01 00:00:00', $month . '-' . $bookingsCount['days_in_month'] . ' 23:59:59'])->count();
        $bookingsDone = TruckBooking::where('status', 'done')
            ->whereBetween('created_at', [$month . '-01 00:00:00', $month . '-' . $bookingsCount['days_in_month'] . ' 23:59:59'])->count();
        $bookingsCancels = TruckBooking::where('status', 'canceled')
            ->whereBetween('created_at', [$month . '-01 00:00:00', $month . '-' . $bookingsCount['days_in_month'] . ' 23:59:59'])->count();

        $userPermissions = [];
        $user = $request->user();
        if ($user) {
            $adminRole = $user->adminRole;
            if ($adminRole) {
                $userPermissions = json_decode($user->adminRole->permissions, true);
            }
        }

        return view('admin.statistics.bookings.month', [
            'bookings_count' => $bookingsCount,
            'date' => $month,
            'bookings_cancel_count' => $bookingsCancelsCount,
            'bookings_awaiting' => $bookingsAwaiting,
            'bookings_in_progress' => $bookingsInProgress,
            'bookings_done' => $bookingsDone,
            'bookings_cancels' => $bookingsCancels,
            'user_permissions' => $userPermissions,
        ]);
    }

    public function usersYearDetails(Request $request)
    {
        $year = $request->input('date', now()->format('Y'));

        $driversActive = Car::where('active', 1)->where('moderated', 1)
            ->whereBetween('created_at', [$year . '-01-01 00:00:00', $year . '-12-31 23:59:59'])->count();
        $driversModeration = Car::where('moderated', 0)
            ->whereBetween('created_at', [$year . '-01-01 00:00:00', $year . '-12-31 23:59:59'])->count();
        $driversBlocked = Car::where('active', 0)
            ->whereBetween('created_at', [$year . '-01-01 00:00:00', $year . '-12-31 23:59:59'])->count();

        $usersCount = $this->year('users', $year);
        $driversCount = $this->year('drivers', $year);
        $companiesCount = $this->year('companies', $year);

        $userPermissions = [];
        $user = $request->user();
        if ($user) {
            $adminRole = $user->adminRole;
            if ($adminRole) {
                $userPermissions = json_decode($user->adminRole->permissions, true);
            }
        }

        return view('admin.statistics.users.year', [
            'users_count' => $usersCount,
            'date' => $year,
            'drivers_count' => $driversCount,
            'companies_count' => $companiesCount,
            'drivers_active' => $driversActive,
            'drivers_moderation' => $driversModeration,
            'drivers_blocked' => $driversBlocked,
//            'bookings_cancels' => $bookingsCancels,
            'user_permissions' => $userPermissions,
        ]);
    }

    public function usersMonthDetails(Request $request)
    {
        $month = $request->input('date', now()->format('Y-m'));

        $usersCount = $this->month('users', $month);
        $driversCount = $this->month('drivers', $month);
        $companiesCount = $this->month('companies', $month);

        $driversActive = Car::where('active', 1)->where('moderated', 1)
            ->whereBetween('created_at', [$month . '-01 00:00:00', $month . '-' . $usersCount['days_in_month'] . ' 23:59:59'])->count();
        $driversModeration = Car::where('moderated', 0)
            ->whereBetween('created_at', [$month . '-01 00:00:00', $month . '-' . $usersCount['days_in_month'] . ' 23:59:59'])->count();
        $driversBlocked = Car::where('active', 0)
            ->whereBetween('created_at', [$month . '-01 00:00:00', $month . '-' . $usersCount['days_in_month'] . ' 23:59:59'])->count();


        $userPermissions = [];
        $user = $request->user();
        if ($user) {
            $adminRole = $user->adminRole;
            if ($adminRole) {
                $userPermissions = json_decode($user->adminRole->permissions, true);
            }
        }

        return view('admin.statistics.users.month', [
            'users_count' => $usersCount,
            'date' => $month,
            'drivers_count' => $driversCount,
            'companies_count' => $companiesCount,
            'drivers_active' => $driversActive,
            'drivers_moderation' => $driversModeration,
            'drivers_blocked' => $driversBlocked,
            'user_permissions' => $userPermissions,
        ]);
    }

    public function sumsYearDetails(Request $request)
    {
        $year = $request->input('date', now()->format('Y'));

        $bookingsSum = TruckBooking::where('status', 'done')
            ->whereBetween('created_at', [$year . '-01-01 00:00:00', $year . '-12-31 23:59:59'])->sum('price');
        $bookingsCommission = TruckBooking::where('status', 'done')
            ->whereBetween('created_at', [$year . '-01-01 00:00:00', $year . '-12-31 23:59:59'])->sum('commission');

        $bookingsSumCount = $this->year('sums', $year);
        $bookingsCommissionCount = $this->year('commissions', $year);

        return view('admin.statistics.sums.year', [
            'sums_count' => $bookingsSumCount,
            'date' => $year,
            'commission_count' => $bookingsCommissionCount,
            'sums_total' => $bookingsSum,
            'commission_total' => $bookingsCommission,
        ]);
    }

    public function sumsMonthDetails(Request $request)
    {
        $month = $request->input('date', now()->format('Y-m'));

        $bookingsSumCount = $this->month('sums', $month);
        $bookingsCommissionCount = $this->month('commissions', $month);

        $bookingsSum = TruckBooking::where('status', 'done')
            ->whereBetween('created_at', [$month . '-01 00:00:00', $month . '-' . $bookingsSumCount['days_in_month'] . ' 23:59:59'])->sum('price');
        $bookingsCommission = TruckBooking::where('status', 'done')
            ->whereBetween('created_at', [$month . '-01 00:00:00', $month . '-' . $bookingsSumCount['days_in_month'] . ' 23:59:59'])->sum('commission');

        return view('admin.statistics.sums.month', [
            'sums_count' => $bookingsSumCount,
            'date' => $month,
            'commission_count' => $bookingsCommissionCount,
            'sums_total' => $bookingsSum,
            'commission_total' => $bookingsCommission,
        ]);
    }

    public function regionsDetails(Request $request)
    {
        $preset = $request->input('preset', 'week');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        if ($preset == 'week') {
            $dateStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'month') {
            $dateStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'year') {
            $dateStart = date('Y-m-d 00:00:00', now()->subYear()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'all') {
            $dateStart = null;
            $dateEnd = null;
        }
        $popularRegionFrom = DB::select("
        select region_from_id, count(id) as cnt from truck_bookings
        group by region_from_id
        order by cnt desc");
        $popularRegionTo = DB::select("
        select  region_to_id, count(id) as cnt from truck_bookings
        group by  region_to_id
        order by cnt desc");
        if ($dateStart && $dateEnd) {
            $popularRegionFrom = DB::select("
                select region_from_id , count(id) as cnt from truck_bookings
                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id
                order by cnt desc");
            $popularRegionTo = DB::select("
                select  region_to_id, count(id) as cnt from truck_bookings
                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by  region_to_id
                order by cnt desc");
        } elseif ($dateStart) {
            $popularRegionFrom = DB::select("
                select region_from_id,  count(id) as cnt from truck_bookings
                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by region_from_id
                order by cnt desc");
            $popularRegionTo = DB::select("
                select  region_to_id, count(id) as cnt from truck_bookings
                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by  region_to_id
                order by cnt desc");
        } elseif ($dateEnd) {
            $popularRegionFrom = DB::select("
                select region_from_id , count(id) as cnt from truck_bookings
                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id
                order by cnt desc");
            $popularRegionTo = DB::select("
                select  region_to_id, count(id) as cnt from truck_bookings
                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by  region_to_id
                order by cnt desc");
        }

        return view('admin.statistics.regions.graphic', [
            'preset' => $preset,
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'popular_regions_from' => collect($popularRegionFrom),
            'popular_regions_to' => collect($popularRegionTo),
        ]);
    }

    public function directionsDetails(Request $request)
    {

        $preset = $request->input('preset', 'week');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $regionFromId = $request->input('region_from_id');
        $regionToId = $request->input('region_to_id');

        if ($preset == 'week') {
            $dateStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'month') {
            $dateStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'year') {
            $dateStart = date('Y-m-d 00:00:00', now()->subYear()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'all') {
            $dateStart = null;
            $dateEnd = null;
        }

        $bookingsCount = TruckBooking::count();

        $whereQuery = "";

        if ($regionFromId && $regionToId) {
            $whereQuery .= " where region_from_id='" . $regionFromId . "' and region_to_id='" . $regionToId . "' ";
        } elseif ($regionFromId) {
            $whereQuery .= " where region_from_id='" . $regionFromId . "' ";
        } elseif ($regionToId) {
            $whereQuery .= " where region_to_id='" . $regionToId . "' ";
        }

        $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                " . $whereQuery . "
                group by region_from_id, region_to_id
                order by cnt desc");

        if ($dateStart && $dateEnd) {

            $bookingsCount = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->count();

            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                " . ($whereQuery == '' ? ' where ' : $whereQuery . ' and ') . "
                 created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id, region_to_id
                order by cnt desc");

        } elseif ($dateStart) {

            $bookingsCount = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                " . ($whereQuery == '' ? ' where ' : $whereQuery . ' and ') . "
                  created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by region_from_id, region_to_id
                order by cnt desc");

        } elseif ($dateEnd) {
            $bookingsCount = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                " . ($whereQuery == '' ? ' where ' : $whereQuery . ' and ') . "
                  created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id, region_to_id
                order by cnt desc");
        }

        return view('admin.statistics.directions.graphic', [
            'preset' => $preset,
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'popular_directions' => collect($popularDirections),
            'bookings_count' => $bookingsCount,
            'region_from_id' => $regionFromId,
            'region_to_id' => $regionToId,
        ]);
    }

    public function directionsDetailsExport(Request $request)
    {

        $preset = $request->input('preset', 'week');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $regionFromId = $request->input('region_from_id');
        $regionToId = $request->input('region_to_id');

        if ($preset == 'week') {
            $dateStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'month') {
            $dateStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'year') {
            $dateStart = date('Y-m-d 00:00:00', now()->subYear()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'all') {
            $dateStart = null;
            $dateEnd = null;
        }

        $bookingsCount = TruckBooking::count();

        $whereQuery = "";

        if ($regionFromId && $regionToId) {
            $whereQuery .= " where region_from_id='" . $regionFromId . "' and region_to_id='" . $regionToId . "' ";
        } elseif ($regionFromId) {
            $whereQuery .= " where region_from_id='" . $regionFromId . "' ";
        } elseif ($regionToId) {
            $whereQuery .= " where region_to_id='" . $regionToId . "' ";
        }

        $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                " . $whereQuery . "
                group by region_from_id, region_to_id
                order by cnt desc");

        if ($dateStart && $dateEnd) {

            $bookingsCount = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->count();

            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                " . ($whereQuery == '' ? ' where ' : $whereQuery . ' and ') . "
                 created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id, region_to_id
                order by cnt desc");

        } elseif ($dateStart) {

            $bookingsCount = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                " . ($whereQuery == '' ? ' where ' : $whereQuery . ' and ') . "
                  created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by region_from_id, region_to_id
                order by cnt desc");

        } elseif ($dateEnd) {
            $bookingsCount = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                " . ($whereQuery == '' ? ' where ' : $whereQuery . ' and ') . "
                  created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id, region_to_id
                order by cnt desc");
        }

        $exportData = collect();
        $exportData->push([
            'Направоение',
            'Кол. заказов',
            '',
        ]);

        foreach (collect($popularDirections) as $popularDirection) {

            $regionFrom = Region::find($popularDirection->region_from_id);
            $regionTo = Region::find($popularDirection->region_to_id);

            $userData = [
                ($regionFrom->title ?? 'Не определено').' - '.($regionTo->title ?? 'Не определено'),
                $popularDirection->cnt,
                ($bookingsCount > 0) ? round($popularDirection->cnt * 100 / $bookingsCount, 2) : 0,
            ];
            $exportData->push($userData);
        }

        $export = new UsersExport($exportData);

        return Excel::download($export, config('app.name') . ' - directions -  ' . (date('Y-m-d')) . '.xlsx');
    }

    public function accountsDriversDetails(Request $request)
    {
        $preset = $request->input('preset', 'all');

        $users = User::whereNotIn('role', ['admin', 'merchant'])->whereHas('car')
            ->with('profile')
            ->with('car')
            ->orderBy('last_seen_at', 'desc')
            ->paginate()->appends(['preset' => $preset]);


        if ($preset == 'week') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])->whereHas('car')
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subWeek()->timestamp))),
                    date('Y-m-d 23:59:59', strtotime(date('Y-m-d 23:59:59'))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->paginate()->appends(['preset' => $preset]);
        }

        if ($preset == 'month') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])->whereHas('car')
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonth()->timestamp))),
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subWeek()->timestamp))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->paginate()->appends(['preset' => $preset]);
        }
        if ($preset == 'months') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])->whereHas('car')
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonths(3)->timestamp))),
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonth()->timestamp))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->paginate()->appends(['preset' => $preset]);
        }

        return view('admin.statistics.accounts.drivers', [
            'users' => $users,
            'preset' => $preset
        ]);
    }

    public function accountsUsersDetails(Request $request)
    {
        $preset = $request->input('preset', 'all');

        $users = User::whereNotIn('role', ['admin', 'merchant'])
            ->with('profile')
            ->with('car')
            ->orderBy('last_seen_at', 'desc')
            ->paginate()->appends(['preset' => $preset]);

        if ($preset == 'week') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subWeek()->timestamp))),
                    date('Y-m-d 23:59:59', strtotime(date('Y-m-d 23:59:59'))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->paginate()->appends(['preset' => $preset]);
        }

        if ($preset == 'month') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonth()->timestamp))),
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subWeek()->timestamp))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->paginate()->appends(['preset' => $preset]);
        }
        if ($preset == 'months') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonths(3)->timestamp))),
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonth()->timestamp))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->paginate()->appends(['preset' => $preset]);
        }

        return view('admin.statistics.accounts.users', [
            'users' => $users,
            'preset' => $preset
        ]);
    }

    public function accountsDriversDetailsExport(Request $request)
    {

        $preset = $request->input('preset', 'all');

        $users = User::whereNotIn('role', ['admin', 'merchant'])->whereHas('car')
            ->with('profile')
            ->with('car')
            ->orderBy('last_seen_at', 'desc')
            ->get();


        if ($preset == 'week') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])->whereHas('car')
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subWeek()->timestamp))),
                    date('Y-m-d 23:59:59', strtotime(date('Y-m-d 23:59:59'))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->get();
        }

        if ($preset == 'month') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])->whereHas('car')
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonth()->timestamp))),
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subWeek()->timestamp))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->get();
        }
        if ($preset == 'months') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])->whereHas('car')
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonths(3)->timestamp))),
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonth()->timestamp))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->get();
        }

        $exportData = collect();
        $exportData->push([
            'ID',
            'Ф.И.О',
            'Тип транспорт',
            'Рейтинг',
            'Дата регистрации',
            'Заказов',
            'Последний раз был в сети',
        ]);

        foreach ($users as $user) {
            $userData = [
                $user->id ?? $user->car->id,
                ($user->profile->surname ?? '') . ' ' . ($user->profile->name ?? '') . ' ' . ($user->profile->middle_name ?? ''),
                ($user->car && $user->car->carType) ? $user->car->carType->title : 'Не узакан',
                $user->profile ? ($user->profile->rating ?? 'Нет оценки') : 'Нет оценки',
                ($user->car && $user->car->created_at) ? $user->car->created_at->format('d.m.Y') : '',
                TruckBooking::where('driver_id', $user->id)->count() ?? 0,
                $user->last_seen_at ? $user->last_seen_at->format('d.m.Y') : 'Не был в сети',
            ];
            $exportData->push($userData);
        }

        $export = new UsersExport($exportData);

        return Excel::download($export, config('app.name') . ' - accounts - drivers ' . (date('Y-m-d')) . '.xlsx');
    }


    public function accountsUsersDetailsExport(Request $request)
    {
        $preset = $request->input('preset', 'all');

        $users = User::whereNotIn('role', ['admin', 'merchant'])
            ->with('profile')
            ->with('car')
            ->orderBy('last_seen_at', 'desc')
            ->get();

        if ($preset == 'week') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subWeek()->timestamp))),
                    date('Y-m-d 23:59:59', strtotime(date('Y-m-d 23:59:59'))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->get();
        }

        if ($preset == 'month') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonth()->timestamp))),
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subWeek()->timestamp))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->get();
        }
        if ($preset == 'months') {
            $users = User::whereNotIn('role', ['admin', 'merchant'])
                ->whereBetween('last_seen_at', [
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonths(3)->timestamp))),
                    date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subMonth()->timestamp))),
                ])
                ->with('profile')
                ->with('car')
                ->orderBy('last_seen_at', 'desc')
                ->get();
        }

        $exportData = collect();
        $exportData->push([
            'ID',
            'Ф.И.О',
//            'Тип транспорт',
            'Рейтинг',
            'Дата регистрации',
            'Заказов',
            'Последний раз был в сети',
        ]);

        foreach ($users as $user) {
            $userData = [
                $user->id ?? '',
                ($user->profile->surname ?? '') . ' ' . ($user->profile->name ?? '') . ' ' . ($user->profile->middle_name ?? ''),
//                ($user->car && $user->car->carType) ? $user->car->carType->title: 'Не узакан',
                $user->profile ? ($user->profile->rating ?? 'Нет оценки') : 'Нет оценки',
                ($user->created_at) ? $user->created_at->format('d.m.Y') : '',
                TruckBooking::where('user_id', $user->id)->count() ?? 0,
                $user->last_seen_at ? $user->last_seen_at->format('d.m.Y') : 'Не был в сети',
            ];
            $exportData->push($userData);
        }

        $export = new UsersExport($exportData);

        return Excel::download($export, config('app.name') . ' - accounts - users ' . (date('Y-m-d')) . '.xlsx');
    }


    public function usersByRegions(Request $request)
    {
        $preset = $request->input('preset', 'week');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        if ($preset == 'week') {
            $dateStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'month') {
            $dateStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'year') {
            $dateStart = date('Y-m-d 00:00:00', now()->subYear()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'all') {
            $dateStart = null;
            $dateEnd = null;
        }

        $userByRegionCount = DB::select("
        select region_id, count(id) as cnt from user_profiles
        group by region_id
        order by cnt desc ");

        if ($dateStart && $dateEnd) {
            $userByRegionCount = DB::select("
                select region_id, count(id) as cnt from user_profiles
                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_id
                order by cnt desc ");
        } elseif ($dateStart) {

            $userByRegionCount = DB::select("
                select region_id, count(id) as cnt from user_profiles
                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by region_id
                order by cnt desc ");
        } elseif ($dateEnd) {

            $userByRegionCount = DB::select("
                select region_id, count(id) as cnt from user_profiles
                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_id
                order by cnt desc ");

        }

        $userByRegionDivided = [];

        foreach (collect($userByRegionCount) as $userByRegion) {
            $totalCount = User::whereNotIn('role', ['admin', 'merchant'])
                ->whereHas('profile', function ($query) use ($userByRegion) {
                    $query->where('region_id', $userByRegion->region_id);
                })->count();
            $driversCount = User::whereHas('car')
                ->whereHas('profile', function ($query) use ($userByRegion) {
                    $query->where('region_id', $userByRegion->region_id);
                })->count();

            $userByRegionDivided[$userByRegion->region_id] = [
                'users_count' => $totalCount - $driversCount ?? 0,
                'drivers_count' => $driversCount ?? 0,
                'total_count' => $totalCount ?? 0,
            ];
        }

        return view('admin.statistics.regions.users', [
            'preset' => $preset,
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'user_by_region_count' => collect($userByRegionCount),
            'user_by_region_divided' => collect($userByRegionDivided),
        ]);
    }

    public function avgTime(Request $request)
    {
        $preset = $request->input('preset', 'week');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        if ($preset == 'week') {
            $dateStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'month') {
            $dateStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'year') {
            $dateStart = date('Y-m-d 00:00:00', now()->subYear()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'all') {
            $dateStart = null;
            $dateEnd = null;
        }

        $carTypes = CarType::whereHas('truck_bookings', function ($q) {
            $q->whereNotNull('accepting_time');
        })->get();

        $cargoTypes = CargoType::whereHas('truck_bookings', function ($q) {
            $q->whereNotNull('accepting_time');
        })->get();

        $acceptingTime = [];
        $loadingTime = [];
        $unloadingTime = [];

        $bookings = TruckBooking::withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with('user', 'driver')
            ->paginate()->appends(['preset' => $preset, 'date_start' => $dateStart, 'date_end' => $dateEnd]);

        foreach ($carTypes as $carType) {
            foreach ($cargoTypes as $cargoType) {
                $acceptingTime[$carType->title ?? $carType->id][$cargoType->title ?? $cargoType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('car_type_id', $carType->id)
                    ->where('cargo_type_id', $cargoType->id)
                    ->avg('accepting_time');
            }
            $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
            $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
        }

        if ($dateStart && $dateEnd) {


            $bookings = TruckBooking::whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->paginate()->appends(['preset' => $preset, 'date_start' => $dateStart, 'date_end' => $dateEnd]);


            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateStart, $dateEnd) {
                $q->whereNotNull('accepting_time')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ]);
            })->get();

            $cargoTypes = CargoType::whereHas('truck_bookings', function ($q) use ($dateStart, $dateEnd) {
                $q->whereNotNull('accepting_time')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ]);
            })->get();

            $acceptingTime = [];
            $loadingTime = [];
            $unloadingTime = [];
            foreach ($carTypes as $carType) {
                foreach ($cargoTypes as $cargoType) {
                    $acceptingTime[$carType->title ?? $carType->id][$cargoType->title ?? $cargoType->id] = TruckBooking::whereNotNull('accepting_time')
                        ->whereBetween('created_at', [
                            date('Y-m-d 00:00:00', strtotime($dateStart)),
                            date('Y-m-d 23:59:59', strtotime($dateEnd)),
                        ])
                        ->where('car_type_id', $carType->id)
                        ->where('cargo_type_id', $cargoType->id)
                        ->avg('accepting_time');
                }

                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->whereBetween('created_at', [
                        date('Y-m-d 00:00:00', strtotime($dateStart)),
                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
                    ])
                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->whereBetween('created_at', [
                        date('Y-m-d 00:00:00', strtotime($dateStart)),
                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
                    ])
                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
            }
        } elseif ($dateStart) {

            $bookings = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->paginate()->appends(['preset' => $preset, 'date_start' => $dateStart, 'date_end' => $dateEnd]);

            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateStart) {
                $q->whereNotNull('accepting_time')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)));
            })->get();

            $cargoTypes = CargoType::whereHas('truck_bookings', function ($q) use ($dateStart) {
                $q->whereNotNull('accepting_time')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)));
            })->get();

            $acceptingTime = [];
            $loadingTime = [];
            $unloadingTime = [];
            foreach ($carTypes as $carType) {

                foreach ($cargoTypes as $cargoType) {
                    $acceptingTime[$carType->title ?? $carType->id][$cargoType->title ?? $cargoType->id] = TruckBooking::whereNotNull('accepting_time')
                        ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                        ->where('car_type_id', $carType->id)
                        ->where('cargo_type_id', $cargoType->id)
                        ->avg('accepting_time');
                }

                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
            }

        } elseif ($dateEnd) {

            $bookings = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->paginate()->appends(['preset' => $preset, 'date_start' => $dateStart, 'date_end' => $dateEnd]);

            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateEnd) {
                $q->whereNotNull('accepting_time')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)));
            })->get();

            $cargoTypes = CargoType::whereHas('truck_bookings', function ($q) use ($dateEnd) {
                $q->whereNotNull('accepting_time')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)));
            })->get();
            $acceptingTime = [];
            $loadingTime = [];
            $unloadingTime = [];
            foreach ($carTypes as $carType) {

                foreach ($cargoTypes as $cargoType) {
                    $acceptingTime[$carType->title ?? $carType->id][$cargoType->title ?? $cargoType->id] = TruckBooking::whereNotNull('accepting_time')
                        ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                        ->where('car_type_id', $carType->id)
                        ->where('cargo_type_id', $cargoType->id)
                        ->avg('accepting_time');
                }
                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
            }
        }

        return view('admin.statistics.avg-time.index', [
            'preset' => $preset,
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'bookings' => $bookings,
            'accepting_time' => collect($acceptingTime),
            'loading_time' => collect($loadingTime),
            'unloading_time' => collect($unloadingTime),
        ]);
    }


    public function avgTimeExport(Request $request)
    {
        $preset = $request->input('preset', 'week');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        if ($preset == 'week') {
            $dateStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'month') {
            $dateStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'year') {
            $dateStart = date('Y-m-d 00:00:00', now()->subYear()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'all') {
            $dateStart = null;
            $dateEnd = null;
        }

        $carTypes = CarType::whereHas('truck_bookings', function ($q) {
            $q->whereNotNull('accepting_time');
        })->get();

        $cargoTypes = CargoType::whereHas('truck_bookings', function ($q) {
            $q->whereNotNull('accepting_time');
        })->get();

        $acceptingTime = [];
        $loadingTime = [];
        $unloadingTime = [];

        $bookings = TruckBooking::withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with('user', 'driver')
            ->get();

        foreach ($carTypes as $carType) {
            foreach ($cargoTypes as $cargoType) {
                $acceptingTime[$carType->title ?? $carType->id][$cargoType->title ?? $cargoType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('car_type_id', $carType->id)
                    ->where('cargo_type_id', $cargoType->id)
                    ->avg('accepting_time');
            }
            $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
            $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
        }

        if ($dateStart && $dateEnd) {


            $bookings = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->get();


            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateStart, $dateEnd) {
                $q->whereNotNull('accepting_time')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ]);
            })->get();

            $cargoTypes = CargoType::whereHas('truck_bookings', function ($q) use ($dateStart, $dateEnd) {
                $q->whereNotNull('accepting_time')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ]);
            })->get();

            $acceptingTime = [];
            $loadingTime = [];
            $unloadingTime = [];
            foreach ($carTypes as $carType) {
                foreach ($cargoTypes as $cargoType) {
                    $acceptingTime[$carType->title ?? $carType->id][$cargoType->title ?? $cargoType->id] = TruckBooking::whereNotNull('accepting_time')
                        ->whereBetween('created_at', [
                            date('Y-m-d 00:00:00', strtotime($dateStart)),
                            date('Y-m-d 23:59:59', strtotime($dateEnd)),
                        ])
                        ->where('car_type_id', $carType->id)
                        ->where('cargo_type_id', $cargoType->id)
                        ->avg('accepting_time');
                }

                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->whereBetween('created_at', [
                        date('Y-m-d 00:00:00', strtotime($dateStart)),
                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
                    ])
                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->whereBetween('created_at', [
                        date('Y-m-d 00:00:00', strtotime($dateStart)),
                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
                    ])
                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
            }
        } elseif ($dateStart) {

            $bookings = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->get();

            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateStart) {
                $q->whereNotNull('accepting_time')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)));
            })->get();

            $cargoTypes = CargoType::whereHas('truck_bookings', function ($q) use ($dateStart) {
                $q->whereNotNull('accepting_time')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)));
            })->get();

            $acceptingTime = [];
            $loadingTime = [];
            $unloadingTime = [];
            foreach ($carTypes as $carType) {

                foreach ($cargoTypes as $cargoType) {
                    $acceptingTime[$carType->title ?? $carType->id][$cargoType->title ?? $cargoType->id] = TruckBooking::whereNotNull('accepting_time')
                        ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                        ->where('car_type_id', $carType->id)
                        ->where('cargo_type_id', $cargoType->id)
                        ->avg('accepting_time');
                }

                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
            }

        } elseif ($dateEnd) {

            $bookings = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->get();

            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateEnd) {
                $q->whereNotNull('accepting_time')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)));
            })->get();

            $cargoTypes = CargoType::whereHas('truck_bookings', function ($q) use ($dateEnd) {
                $q->whereNotNull('accepting_time')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)));
            })->get();
            $acceptingTime = [];
            $loadingTime = [];
            $unloadingTime = [];
            foreach ($carTypes as $carType) {

                foreach ($cargoTypes as $cargoType) {
                    $acceptingTime[$carType->title ?? $carType->id][$cargoType->title ?? $cargoType->id] = TruckBooking::whereNotNull('accepting_time')
                        ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                        ->where('car_type_id', $carType->id)
                        ->where('cargo_type_id', $cargoType->id)
                        ->avg('accepting_time');
                }
                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
            }
        }

        $acceptingTimeExport = collect();
        $titleData = [''];

        foreach (collect($acceptingTime) as $title => $byCargoTypes) {
            foreach ($byCargoTypes as $cTTitle => $min) {
                $titleData[] = $cTTitle;
            }
        }
        $acceptingTimeExport->push($titleData);

        foreach (collect($acceptingTime) as $title => $byCargoTypes) {
            $acceptingTimeExportData = [$title];
            foreach ($byCargoTypes as $cTTitle => $min) {
                $acceptingTimeExportData[] = ((int) $min ?? 0).' мин';
            }
            $acceptingTimeExport->push($acceptingTimeExportData);
        }

        $loadingTimeExport = collect();
        $loadingTimeExport->push([
            '',
            'Погрузка',
            'Разгрузка',
        ]);

        foreach (collect($loadingTime) as $title => $min) {
            $acceptingTimeExportData = [
                $title,
                ((int) $min ?? 0).' мин',
                ((int) $unloadingTime[$title] ?? 0).' мин',
            ];
            $loadingTimeExport->push($acceptingTimeExportData);
        }


        $exportData = collect();
        $exportData->push([
            'ID',
            'Точка А/Точка Б',
            'Тип груза',
            'Тип авто',
            'Время создания',
            'Время принятия',
            'Время погрузки',
            'Время разгрузки',
            'Время в пути',
        ]);

        foreach ($bookings as $booking) {

            $routes = json_decode($booking->routes ?? '', true);
            $regionFrom = isset($routes[0]) && isset($routes[0]['address']) ? $routes[0]['address'] : 'Адрес не указан';
            $regionTo = isset($routes[1]) && isset($routes[1]['address']) ? $routes[1]['address'] : 'Адрес не указан';

            $bookingData = [
                $booking->id ?? '',
                ($regionFrom ?? 'Регион не определен') . ' - ' . ($regionTo ?? 'Регион не определен'),
                ($booking->cargoType->title ?? '--') . ' / ' . (round(($booking->weight ?? 0) / 1000, 3).' т.'),
                $booking->carType->title ?? '--',
                $booking->created_at ? $booking->created_at->format('d.m.Y H:i') : '--',
                $booking->accepting_time ? $booking->accepting_time.' мин' : '--',
                $booking->pickup_waiting_time ? $booking->pickup_waiting_time.' мин' : '--',
                $booking->unloading_waiting_time ? $booking->unloading_waiting_time.' мин' : '--',
                $booking->driving_time ? $booking->driving_time.' мин' : '--',
            ];
            $exportData->push($bookingData);
        }

        $export = new MultiSheetExport($acceptingTimeExport, $loadingTimeExport, $exportData);

        return Excel::download($export, config('app.name') . ' - avg times -  ' . (date('Y-m-d')) . '.xlsx');



    }

    public function cancelReasonsDetails(Request $request)
    {
        $preset = $request->input('preset', 'week');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        if ($preset == 'week') {
            $dateStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'month') {
            $dateStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'year') {
            $dateStart = date('Y-m-d 00:00:00', now()->subYear()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'all') {
            $dateStart = null;
            $dateEnd = null;
        }
        $cancelReasons = CancelReason::all();

        $cancelReasonsCount = [
            'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')->count(),
        ];
        foreach ($cancelReasons as $cancelReason) {
            $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)->count();
        }

        $bookings = TruckBooking::where('status', TruckBooking::STATUS_CANCELED)
            ->withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with('user', 'driver')
            ->paginate()->appends(['preset' => $preset, 'date_start' => $dateStart, 'date_end' => $dateEnd]);

        if ($dateStart && $dateEnd) {
            $bookings = TruckBooking::where('status', TruckBooking::STATUS_CANCELED)
                ->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->paginate()->appends(['preset' => $preset, 'date_start' => $dateStart, 'date_end' => $dateEnd]);
            $cancelReasonsCount = [
                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
                    ->whereBetween('created_at', [
                        date('Y-m-d 00:00:00', strtotime($dateStart)),
                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
                    ])->count(),
            ];
            foreach ($cancelReasons as $cancelReason) {
                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])->count();
            }

        } elseif ($dateStart) {
            $bookings = TruckBooking::where('status', TruckBooking::STATUS_CANCELED)
                ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->paginate()->appends(['preset' => $preset, 'date_start' => $dateStart, 'date_end' => $dateEnd]);
            $cancelReasonsCount = [
                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count(),
            ];
            foreach ($cancelReasons as $cancelReason) {
                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            }

        } elseif ($dateEnd) {
            $bookings = TruckBooking::where('status', TruckBooking::STATUS_CANCELED)
                ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->paginate()->appends(['preset' => $preset, 'date_start' => $dateStart, 'date_end' => $dateEnd]);
            $cancelReasonsCount = [
                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count(),
            ];
            foreach ($cancelReasons as $cancelReason) {
                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            }

        }

        return view('admin.statistics.cancel-reasons.index', [
            'preset' => $preset,
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'bookings' => $bookings,
            'cancel_reason_count' => collect($cancelReasonsCount),
        ]);
    }

    public function cancelReasonsDetailsExport(Request $request)
    {
        $preset = $request->input('preset', 'week');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        if ($preset == 'week') {
            $dateStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'month') {
            $dateStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'year') {
            $dateStart = date('Y-m-d 00:00:00', now()->subYear()->timestamp);
            $dateEnd = date('Y-m-d 23:59:59');
        } elseif ($preset == 'all') {
            $dateStart = null;
            $dateEnd = null;
        }
        $cancelReasons = CancelReason::all();

        $cancelReasonsCount = [
            'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')->count(),
        ];
        foreach ($cancelReasons as $cancelReason) {
            $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)->count();
        }

        $bookings = TruckBooking::where('status', TruckBooking::STATUS_CANCELED)
            ->withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with('user', 'driver')
            ->get();

        if ($dateStart && $dateEnd) {
            $bookings = TruckBooking::where('status', TruckBooking::STATUS_CANCELED)
                ->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->get();
            $cancelReasonsCount = [
                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
                    ->whereBetween('created_at', [
                        date('Y-m-d 00:00:00', strtotime($dateStart)),
                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
                    ])->count(),
            ];
            foreach ($cancelReasons as $cancelReason) {
                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])->count();
            }

        } elseif ($dateStart) {
            $bookings = TruckBooking::where('status', TruckBooking::STATUS_CANCELED)
                ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->get();
            $cancelReasonsCount = [
                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count(),
            ];
            foreach ($cancelReasons as $cancelReason) {
                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            }

        } elseif ($dateEnd) {
            $bookings = TruckBooking::where('status', TruckBooking::STATUS_CANCELED)
                ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
                ->withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')
                ->get();
            $cancelReasonsCount = [
                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count(),
            ];
            foreach ($cancelReasons as $cancelReason) {
                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            }

        }


        $exportData = collect();
        $exportData->push([
            'ID',
            'Дата',
            'Точка А/Точка Б',
            'Клиент',
            'Водитель',
            'Тип авто',
            'Тип груза',
            'Причина отмены',
            'Комментарий',
        ]);

        foreach ($bookings as $booking) {

            $routes = json_decode($booking->routes ?? '', true);
            $regionFrom = isset($routes[0]) && isset($routes[0]['address']) ? $routes[0]['address'] : 'Адрес не указан';
            $regionTo = isset($routes[1]) && isset($routes[1]['address']) ? $routes[1]['address'] : 'Адрес не указан';

            $data = [
                $booking->id ?? '',
                $booking->created_at ? $booking->created_at->format('d.m.Y H:i') : '--',
                ($regionFrom ?? 'Регион не определен').' - '.($regionTo ?? 'Регион не определен'),
                ($booking->user->surname ?? '').' '.($booking->user->name ?? '').' '.($booking->user->middle_name ?? ''),
                $booking->driver_id ? (($booking->driver->surname ?? '').' '.($booking->driver->name ?? '').' '.($booking->driver->middle_name ?? '')) : 'Водитель не назначен',
                $booking->carType->title ?? '',
                ($booking->cargoType->title ?? 'Тип не указан').' / '.round(($booking->weight ?? 0) / 1000, 3).' т.',
                $booking->cancelReason->reason ?? 'Другое',
                $booking->cancel_reason_comment ?? ''
            ];
            $exportData->push($data);
        }

        $export = new BookingsExport($exportData);

        return Excel::download($export, config('app.name') . ' - cancel_reasons - ' . (date('Y-m-d')) . '.xlsx');
    }

    private function modelsList()
    {
        return [
            'bookings',
            'cancels',
            'sums',
            'commissions',
            'companies',
            'users',
            'drivers',
        ];
    }
}
