<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 05.04.2019
 * Time: 14:23
 */

namespace App\Http\Controllers\Admin;

use App\Domain\Cars\Models\Car;
use App\Domain\Regions\Models\Region;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private function loadDashboardStats(): array
    {
        return [
            'usersCount' => User::query()->whereNotIn('role', ['admin', 'merchant'])->count(),
            'driversCount' => User::query()->whereHas('car')->count(),
            'bookingsCount' => TruckBooking::query()->count(),
            'doneCommissionSum' => TruckBooking::query()
                ->where('status', TruckBooking::STATUS_DONE)
                ->sum('commission'),
            'newBookingsCount' => TruckBooking::query()
                ->whereIn('status', [TruckBooking::STATUS_ORDER, TruckBooking::STATUS_NEW])
                ->count(),
            'inProgressBookingsCount' => TruckBooking::query()
                ->whereIn('status', [
                    TruckBooking::STATUS_WAITING,
                    TruckBooking::STATUS_ACCEPTED,
                    TruckBooking::STATUS_ARRIVED,
                    TruckBooking::STATUS_PROCESSING,
                    TruckBooking::STATUS_PAUSE,
                ])
                ->count(),
            'doneBookingsCount' => TruckBooking::query()
                ->where('status', TruckBooking::STATUS_DONE)
                ->count(),
            'canceledBookingsCount' => TruckBooking::query()
                ->where('status', TruckBooking::STATUS_CANCELED)
                ->count(),
        ];
    }

    public function index(Request $request)
    {
        $userPermissions = [];
        $user = $request->user();
        if ($user) {
            $adminRole = $user->adminRole;
            if ($adminRole) {
                $userPermissions = json_decode($user->adminRole->permissions, true);
            }
        }
        return view('admin.welcome', [
            'user_permissions' => $userPermissions,
            'dashboardStats' => $this->loadDashboardStats(),
        ]);
        //кол-во броней
        //кол-во поль-лей
        //кол-во авто
        //кол-во компаний
    }

    public function map()
    {
        $regions = Region::all();
        return view('admin.map', [
            'regions' => $regions,
        ]);
    }
}
