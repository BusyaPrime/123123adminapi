<?php

namespace App\Http\Controllers\TAdmin;

use App\Domain\CancelReasons\Models\CancelReason;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\Companies\Models\Company;
use App\Domain\Regions\Models\Region;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    private $company = null;

    public function index(Request $request)
    {
        $company = $request->input('_company');
        $this->company = $company;
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
        $year = date('Y', $dateEnd ? strtotime($dateEnd) : time());

        $bookingsCount = TruckBooking::where('company_id', $company->id)->count();
        $bookingsCanceledCount = TruckBooking::where('company_id', $company->id)->where('status', 'canceled')->count();

        $bookingsSum = TruckBooking::where('company_id', $company->id)->where('status', 'done')->sum('price');
        $bookingsCommission = TruckBooking::where('company_id', $company->id)->where('status', 'done')->sum('commission');
        $driversCount = User::whereHas('car')->whereHas('profile', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->count();

        $bookingsSumChart = $this->year('sums', $year);
        $bookingsCommissionChart = $this->year('commissions', $year);

        $popularDirections = DB::select("
        select region_from_id, region_to_id, count(id) as cnt from truck_bookings
        where company_id = ".$company->id."
        group by region_from_id, region_to_id
        order by cnt desc limit 5");

        $popularRegionFrom = DB::select("
        select region_from_id, count(id) as cnt from truck_bookings
        where company_id = ".$company->id."
        group by region_from_id
        order by cnt desc limit 5");
        $popularRegionTo = DB::select("
        select  region_to_id, count(id) as cnt from truck_bookings
        where company_id = ".$company->id."
        group by  region_to_id
        order by cnt desc limit 5");
//
//        $driversRatings = [
//            '5' => 0,
//            '4.99 - 4.0' => 0,
//            '3.99 - 3.0' => 0,
//            '2.99 - 2.0' => 0,
//            '1.99 - 1.0' => 0,
//            '0.99 - 0.01' => 0,
//            'Нет оценки' => 0,
//        ];
//
//        $lastSeenAt = [
//            'Неделя' => 0,
//            'Месяц' => 0,
//            '3 Месяца' => 0
//        ];
//
//        $carTypes = CarType::whereHas('truck_bookings', function ($q) {
//            $q->whereNotNull('accepting_time');
//        })->limit(5)->get();
//
//        $cancelReasons = CancelReason::all();
//
//        $cancelReasonsCount = [
//            'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')->count(),
//        ];
//        foreach ($cancelReasons as $cancelReason) {
//            $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)->count();
//        }



//        $bookingsFullCount = TruckBooking::where('not_full', 0)->count();
//        $bookingsNotFullCount = TruckBooking::where('not_full', 1)->count();
//        $usersCount = User::whereNotIn('role', ['admin', 'merchant'])->count();


//        $bookingsSumTotal = TruckBooking::where('status', 'done')->sum('price');
//        $bookingsSumYearTotal = TruckBooking::where('status', 'done')
//            ->whereBetween('created_at', [
//                date('Y-m-d 00:00:00', now()->subYear()->timestamp),
//                date('Y-m-d 23:59:59', now()->timestamp),
//            ])
//            ->sum('price');
//        $bookingsSumMonthTotal = TruckBooking::where('status', 'done')
//            ->whereBetween('created_at', [
//                date('Y-m-d 00:00:00', now()->subMonth()->timestamp),
//                date('Y-m-d 23:59:59', now()->timestamp),
//            ])
//            ->sum('price');
//        $bookingsSumWeekTotal = TruckBooking::where('status', 'done')
//            ->whereBetween('created_at', [
//                date('Y-m-d 00:00:00', now()->subWeek()->timestamp),
//                date('Y-m-d 23:59:59', now()->timestamp),
//            ])
//            ->sum('price');
//        $bookingsSumDayTotal = TruckBooking::where('status', 'done')
//            ->whereBetween('created_at', [
//                date('Y-m-d 00:00:00', now()->timestamp),
//                date('Y-m-d 23:59:59', now()->timestamp),
//            ])
//            ->sum('price');
//
//        $usersTotalCount = User::whereNotIn('role', ['admin', 'merchant'])->count();
//        $driversTotalCount = User::whereHas('car')->count();

//        foreach ($driversRatings as $r => $rating) {
//            $driversRatings[$r] = User::whereHas('car')
//                ->whereHas('profile', function ($query) use ($r) {
//                    if ($r == '5') {
//                        $query->where('rating', $r);
//                    } elseif ($r == '4.99 - 4.0') {
//                        $query->whereBetween('rating', [4.0, 4.99]);
//                    } elseif ($r == '3.99 - 3.0') {
//                        $query->whereBetween('rating', [3.0, 3.99]);
//                    } elseif ($r == '2.99 - 2.0') {
//                        $query->whereBetween('rating', [2.0, 2.99]);
//                    } elseif ($r == '1.99 - 1.0') {
//                        $query->whereBetween('rating', [1.0, 1.99]);
//                    } elseif ($r == '0.99 - 0.01') {
//                        $query->whereBetween('rating', [0.01, 0.99]);
//                    } elseif ($r == 'Нет оценки') {
//                        $query->whereNull('rating')->orWhere('rating', '')->orWhere('rating', 0);
//                    }
//                })
//                ->count();
//        }
//
//        foreach ($lastSeenAt as $r => $lastSeen) {
//            $query = User::whereNotIn('role', ['admin', 'merchant']);
//
//            if ($r == 'Неделя') {
//                $dStart = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
//                $dEnd = date('Y-m-d 23:59:59');
//                $query->whereBetween('last_seen_at', [
//                    date('Y-m-d 00:00:00', strtotime($dStart)),
//                    date('Y-m-d 23:59:59', strtotime($dEnd)),
//                ]);
//            } elseif ($r == 'Месяц') {
//                $dStart = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
//                $dEnd = date('Y-m-d 00:00:00', now()->subWeek()->timestamp);
//                $query->whereBetween('last_seen_at', [
//                    date('Y-m-d 00:00:00', strtotime($dStart)),
//                    date('Y-m-d 00:00:00', strtotime($dEnd)),
//                ]);
//            } elseif ($r == '3 Месяца') {
//                $dStart = date('Y-m-d 00:00:00', now()->subMonths(3)->timestamp);
//                $dEnd = date('Y-m-d 00:00:00', now()->subMonth()->timestamp);
//                $query->whereBetween('last_seen_at', [
//                    date('Y-m-d 00:00:00', strtotime($dStart)),
//                    date('Y-m-d 00:00:00', strtotime($dEnd)),
//                ]);
//            }
//
//            $lastSeenAt[$r] = $query->count();
//        }
//
//        $acceptingTime = [];
//        $loadingTime = [];
//        $unloadingTime = [];
//        foreach ($carTypes as $carType) {
//            $acceptingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                ->where('car_type_id', $carType->id)->avg('accepting_time');
//            $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
//            $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
//        }
//
//        $companiesCount = Company::count();

//        $userByRegionCount = DB::select("
//        select region_id, count(id) as cnt from user_profiles
//        group by region_id
//        order by cnt desc limit 5");
//        $popularCarTypes = DB::select("
//        select car_type_id, count(id) as cnt from truck_bookings
//        group by car_type_id
//        order by cnt desc limit 5");
//        $popularCargoTypes = DB::select("
//        select cargo_type_id, count(id) as cnt from truck_bookings
//        group by cargo_type_id
//        order by cnt desc limit 5");

        if ($dateStart && $dateEnd) {

//            $cancelReasonsCount = [
//                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
//                    ->whereBetween('created_at', [
//                        date('Y-m-d 00:00:00', strtotime($dateStart)),
//                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
//                    ])->count(),
//            ];
//            foreach ($cancelReasons as $cancelReason) {
//                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)->whereBetween('created_at', [
//                    date('Y-m-d 00:00:00', strtotime($dateStart)),
//                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
//                ])->count();
//            }
//
//            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateStart, $dateEnd) {
//                $q->whereNotNull('accepting_time')->whereBetween('created_at', [
//                    date('Y-m-d 00:00:00', strtotime($dateStart)),
//                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
//                ]);
//            })->limit(5)->get();
//            $acceptingTime = [];
//            $loadingTime = [];
//            $unloadingTime = [];
//            foreach ($carTypes as $carType) {
//                $acceptingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                    ->whereBetween('created_at', [
//                        date('Y-m-d 00:00:00', strtotime($dateStart)),
//                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
//                    ])
//                    ->where('car_type_id', $carType->id)->avg('accepting_time');
//
//                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                    ->whereBetween('created_at', [
//                        date('Y-m-d 00:00:00', strtotime($dateStart)),
//                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
//                    ])
//                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
//                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                    ->whereBetween('created_at', [
//                        date('Y-m-d 00:00:00', strtotime($dateStart)),
//                        date('Y-m-d 23:59:59', strtotime($dateEnd)),
//                    ])
//                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
//            }
//            $bookingsFullCount = TruckBooking::whereBetween('created_at', [
//                date('Y-m-d 00:00:00', strtotime($dateStart)),
//                date('Y-m-d 23:59:59', strtotime($dateEnd)),
//            ])->where('not_full', 0)->count();
//            $bookingsNotFullCount = TruckBooking::whereBetween('created_at', [
//                date('Y-m-d 00:00:00', strtotime($dateStart)),
//                date('Y-m-d 23:59:59', strtotime($dateEnd)),
//            ])->where('not_full', 1)->count();

//            $usersCount = User::whereNotIn('role', ['admin', 'merchant'])
//                ->whereBetween('created_at', [
//                    date('Y-m-d 00:00:00', strtotime($dateStart)),
//                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
//                ])->count();
//            $companiesCount = Company::whereBetween('created_at', [
//                date('Y-m-d 00:00:00', strtotime($dateStart)),
//                date('Y-m-d 23:59:59', strtotime($dateEnd)),
//            ])->count();
//            $userByRegionCount = DB::select("
//                select region_id, count(id) as cnt from user_profiles
//                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
//                group by region_id
//                order by cnt desc limit 5");
//            $popularCarTypes = DB::select("
//                select car_type_id, count(id) as cnt from truck_bookings
//                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
//                group by car_type_id
//                order by cnt desc limit 5");
//            $popularCargoTypes = DB::select("
//                select cargo_type_id, count(id) as cnt from truck_bookings
//                where created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
//                group by cargo_type_id
//                order by cnt desc limit 5");

            $bookingsCount = TruckBooking::where('company_id', $company->id)->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->count();
            $bookingsSum = TruckBooking::where('company_id', $company->id)->where('status', 'done')->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->sum('price');
            $bookingsCommission = TruckBooking::where('company_id', $company->id)->where('status', 'done')->whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->sum('commission');
            $bookingsCanceledCount = TruckBooking::where('company_id', $company->id)->where('status', 'canceled')
                ->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])->count();
            $driversCount = User::whereHas('car')->whereHas('profile', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
                ->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($dateStart)),
                    date('Y-m-d 23:59:59', strtotime($dateEnd)),
                ])->count();
            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                where company_id = ".$company->id." and created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id, region_to_id
                order by cnt desc limit 5");
            $popularRegionFrom = DB::select("
                select region_from_id , count(id) as cnt from truck_bookings
                where company_id = ".$company->id." and  created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id
                order by cnt desc limit 5");
            $popularRegionTo = DB::select("
                select  region_to_id, count(id) as cnt from truck_bookings
                where company_id = ".$company->id." and  created_at between '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "' and '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by  region_to_id
                order by cnt desc limit 5");
        } elseif ($dateStart) {
//            $cancelReasonsCount = [
//                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
//                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count(),
//            ];
//            foreach ($cancelReasons as $cancelReason) {
//                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)
//                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
//            }
//
//
//            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateStart) {
//                $q->whereNotNull('accepting_time')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)));
//            })->limit(5)->get();
//            $acceptingTime = [];
//            $loadingTime = [];
//            $unloadingTime = [];
//            foreach ($carTypes as $carType) {
//                $acceptingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
//                    ->where('car_type_id', $carType->id)->avg('accepting_time');
//
//                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
//                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
//                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
//                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
//            }

//            $bookingsFullCount = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
//                ->where('not_full', 0)->count();
//            $bookingsNotFullCount = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))
//                ->where('not_full', 1)->count();

//            $usersCount = User::whereNotIn('role', ['admin', 'merchant'])->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();

//            $companiesCount = Company::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();


//            $userByRegionCount = DB::select("
//                select region_id, count(id) as cnt from user_profiles
//                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
//                group by region_id
//                order by cnt desc limit 5");
//            $popularCarTypes = DB::select("
//                select car_type_id, count(id) as cnt from truck_bookings
//                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
//                group by car_type_id
//                order by cnt desc limit 5");
//            $popularCargoTypes = DB::select("
//                select cargo_type_id, count(id) as cnt from truck_bookings
//                where created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
//                group by cargo_type_id
//                order by cnt desc limit 5");

            $bookingsCount = TruckBooking::where('company_id', $company->id)->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            $bookingsSum = TruckBooking::where('company_id', $company->id)->where('status', 'done')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->sum('price');
            $bookingsCommission = TruckBooking::where('company_id', $company->id)->where('status', 'done')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->sum('commission');
            $bookingsCanceledCount = TruckBooking::where('company_id', $company->id)->where('status', 'canceled')->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            $driversCount = User::whereHas('car')->whereHas('profile', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->count();
            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                where company_id = ".$company->id." and  created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by region_from_id, region_to_id
                order by cnt desc limit 5");
            $popularRegionFrom = DB::select("
                select region_from_id,  count(id) as cnt from truck_bookings
                where company_id = ".$company->id." and  created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by region_from_id
                order by cnt desc limit 5");
            $popularRegionTo = DB::select("
                select  region_to_id, count(id) as cnt from truck_bookings
                where company_id = ".$company->id." and  created_at  >= '" . date('Y-m-d 00:00:00', strtotime($dateStart)) . "'
                group by  region_to_id
                order by cnt desc limit 5");
        } elseif ($dateEnd) {
//            $cancelReasonsCount = [
//                'Другое' => TruckBooking::where('status', TruckBooking::STATUS_CANCELED)->whereNull('cancel_reason_id')
//                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count(),
//            ];
//            foreach ($cancelReasons as $cancelReason) {
//                $cancelReasonsCount[$cancelReason->reason] = TruckBooking::where('cancel_reason_id', $cancelReason->id)
//                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
//            }
//
//            $carTypes = CarType::whereHas('truck_bookings', function ($q) use ($dateEnd) {
//                $q->whereNotNull('accepting_time')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)));
//            })->limit(5)->get();
//            $acceptingTime = [];
//            $loadingTime = [];
//            $unloadingTime = [];
//            foreach ($carTypes as $carType) {
//                $acceptingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
//                    ->where('car_type_id', $carType->id)->avg('accepting_time');
//
//                $loadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
//                    ->where('car_type_id', $carType->id)->avg('pickup_waiting_time');
//                $unloadingTime[$carType->title ?? $carType->id] = TruckBooking::whereNotNull('accepting_time')
//                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
//                    ->where('car_type_id', $carType->id)->avg('unloading_waiting_time');
//            }

//            $bookingsFullCount = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
//                ->where('not_full', 0)->count();
//            $bookingsNotFullCount = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))
//                ->where('not_full', 1)->count();
//            $usersCount = User::whereNotIn('role', ['admin', 'merchant'])->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
//            $companiesCount = Company::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
//            $userByRegionCount = DB::select("
//                select region_id, count(id) as cnt from user_profiles
//                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
//                group by region_id
//                order by cnt desc limit 5");

//            $popularCarTypes = DB::select("
//                select car_type_id, count(id) as cnt from truck_bookings
//                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
//                group by car_type_id
//                order by cnt desc limit 5");
//            $popularCargoTypes = DB::select("
//                select cargo_type_id, count(id) as cnt from truck_bookings
//                where created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
//                group by cargo_type_id
//                order by cnt desc limit 5");
            $bookingsCount = TruckBooking::where('company_id', $company->id)->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            $bookingsSum = TruckBooking::where('company_id', $company->id)->where('status', 'done')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->sum('price');
            $bookingsCommission = TruckBooking::where('company_id', $company->id)->where('status', 'done')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->sum('commission');
            $bookingsCanceledCount = TruckBooking::where('company_id', $company->id)->where('status', 'canceled')->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            $driversCount = User::whereHas('car')->whereHas('profile', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->count();
            $popularDirections = DB::select("
                select region_from_id, region_to_id, count(id) as cnt from truck_bookings
                where company_id = ".$company->id." and  created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id, region_to_id
                order by cnt desc limit 5");
            $popularRegionFrom = DB::select("
                select region_from_id , count(id) as cnt from truck_bookings
                where company_id = ".$company->id." and  created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by region_from_id
                order by cnt desc limit 5");
            $popularRegionTo = DB::select("
                select  region_to_id, count(id) as cnt from truck_bookings
                where company_id = ".$company->id." and  created_at  <= '" . date('Y-m-d 23:59:59', strtotime($dateEnd)) . "'
                group by  region_to_id
                order by cnt desc limit 5");
        }

        $regionTitles = Region::query()
            ->whereIn('id', collect($popularDirections)->pluck('region_from_id')
                ->merge(collect($popularDirections)->pluck('region_to_id'))
                ->merge(collect($popularRegionFrom)->pluck('region_from_id'))
                ->merge(collect($popularRegionTo)->pluck('region_to_id'))
                ->filter()
                ->unique()
                ->values())
            ->pluck('title', 'id');

        return view('admin2.statistics.index', [
            'preset' => $preset,
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'year' => $year,

            'bookings_count' => $bookingsCount,
            'drivers_count' => $driversCount,
            'bookings_canceled_count' => $bookingsCanceledCount,
            'bookings_sum' => $bookingsSum,
            'bookings_commission' => $bookingsCommission,
            'popular_directions' => collect($popularDirections),
            'popular_regions_from' => collect($popularRegionFrom),
            'popular_regions_to' => collect($popularRegionTo),
            'regionTitles' => $regionTitles,
            'bookings_sum_chart' => $bookingsSumChart,
            'bookings_commission_chart' => $bookingsCommissionChart,
//            'users_count' => $usersCount,
//            'companies_count' => $companiesCount,
//            'user_by_region_count' => collect($userByRegionCount),
//            'popular_car_types' => collect($popularCarTypes),
//            'popular_cargo_types' => collect($popularCargoTypes),
//            'bookings_full_count' => $bookingsFullCount,
//            'bookings_not_full_count' => $bookingsNotFullCount,
//            'drivers_ratings' => collect($driversRatings),
//            'drivers_total_count' => $driversTotalCount,
//            'users_total_count' => $usersTotalCount,
//            'last_seen_at' => collect($lastSeenAt),
//            'accepting_time' => collect($acceptingTime),
//            'loading_time' => collect($loadingTime),
//            'unloading_time' => collect($unloadingTime),
//            'cancel_reason_count' => collect($cancelReasonsCount),
//            'bookings_sum_total' => $bookingsSumTotal,
//            'bookings_sum_year_total' => $bookingsSumYearTotal,
//            'bookings_sum_month_total' => $bookingsSumMonthTotal,
//            'bookings_sum_week_total' => $bookingsSumWeekTotal,
//            'bookings_sum_day_total' => $bookingsSumDayTotal,
        ]);
    }

    protected function year($model, $year)
    {
        $company = $this->company;
        $date = $year ?? now()->format('Y');
        $pageTitle = '';
        $countByDate = [];

        for ($i = 1; $i <= 12; $i++) {
            $carbonDate = Date::make($date . '-' . ($i < 10 ? '0' . $i : $i) . '-01');
            $year = $carbonDate->format('Y');
            $month = $carbonDate->format('m');
            $daysInMonth = $carbonDate->daysInMonth;

//            if ($model == 'bookings') {
//                $pageTitle = 'Заказы';
//                $countByDate[$i] = TruckBooking::whereBetween('created_at', [
//                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
//                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
//                ])->count();
//            }
//
//            if ($model == 'cancels') {
//                $pageTitle = 'Отмены';
//                $countByDate[$i] = TruckBooking::where('status', 'canceled')->whereBetween('created_at', [
//                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
//                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
//                ])->count();
//            }

            if ($model == 'sums') {
                $pageTitle = 'Оборот';
                $countByDate[$i] = TruckBooking::where('company_id', $company->id)->where('status', 'done')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
                ])->sum('price');
            }

            if ($model == 'commissions') {
                $pageTitle = 'Отчисления';
                $countByDate[$i] = TruckBooking::where('company_id', $company->id)->where('status', 'done')->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
                ])->sum('commission');
            }

//            if ($model == 'companies') {
//                $pageTitle = 'Компании';
//                $countByDate[$i] = Company::whereBetween('created_at', [
//                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
//                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
//                ])->count();
//            }
//
//            if ($model == 'users') {
//                $pageTitle = 'Пользователи';
//                $countByDate[$i] = User::whereNotIn('role', ['admin', 'merchant'])->whereBetween('created_at', [
//                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
//                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
//                ])->count();
//            }
//
//            if ($model == 'drivers') {
//                $pageTitle = 'Водители';
//                $countByDate[$i] = User::whereHas('car')->whereBetween('created_at', [
//                    date('Y-m-d 00:00:00', strtotime($year . '-' . $month . '-01')),
//                    date('Y-m-d 23:59:59', strtotime($year . '-' . $month . '-' . ($daysInMonth < 10 ? '0' . $daysInMonth : $daysInMonth))),
//                ])->count();
//            }

        }

        return [
            'date' => $date,
            'model' => $model,
//            'page_title' => $pageTitle,
            'count_by_date' => $countByDate,
        ];
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
