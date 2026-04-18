<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 05.04.2019
 * Time: 14:23
 */

namespace App\Http\Controllers\TAdmin;

use App\Domain\TruckBookings\Filters\TruckBookingsFilter;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Models\User;
use App\Domain\Users\Jobs\ApiSetTokenJob;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private function loadCompanyBookingStats($company): object
    {
        return TruckBooking::query()
            ->where('company_id', $company->id)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status = ? THEN price ELSE 0 END), 0) as done_price_sum,
                COALESCE(SUM(CASE WHEN status = ? THEN commission ELSE 0 END), 0) as done_commission_sum,
                SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as waiting_count,
                SUM(CASE WHEN status IN (?, ?, ?, ?, ?, ?) THEN 1 ELSE 0 END) as processing_count,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as done_count,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as canceled_count
            ", [
                TruckBooking::STATUS_DONE,
                TruckBooking::STATUS_DONE,
                TruckBooking::STATUS_ORDER,
                TruckBooking::STATUS_NEW,
                TruckBooking::STATUS_WAITING,
                TruckBooking::STATUS_ACCEPTED,
                TruckBooking::STATUS_ARRIVED,
                TruckBooking::STATUS_PROCESSING,
                TruckBooking::STATUS_PAUSE,
                TruckBooking::STATUS_CONFIRMATION,
                TruckBooking::STATUS_DONE,
                TruckBooking::STATUS_CANCELED,
            ])
            ->first();
    }

    public function index(Request $request)
    {
        $company = $request->input('_company');

        /*
        For Authentication in React App
        */

        if($company->user->is_external == 1){
            if($company->user->tokens->count() == 0){
                $this->dispatch(new ApiSetTokenJob($company->user, $request));
            }
        }

        $company->loadCount('users');

        $users = $company->users()->get();
        $userIds = collect([]);

        foreach($users as $u){
            $userIds->add($u->user_id);
        }
        $userIds->add($company->user->id);

        $filter = new TruckBookingsFilter($request);

        $bookingsCount = TruckBooking::filter($filter)
            ->whereIn('user_id', $userIds)
            ->withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with('user', 'driver')
            ->count();
		/********************/
		$user = auth()->user();
        $userscount = User::leftJoin("user_profiles","users.id","=","user_profiles.user_id")->with('profile')->where("user_profiles.company_id",$company->id)->count();
		/********************/

        $waitingOrders = 0;
        $processingOrders = 0;
        $doneOrders = 0;
        $companyBookingStats = $this->loadCompanyBookingStats($company);

        if($company->user->is_external == 1){
            $waitingOrders = TruckBooking::filter($filter)
            ->whereIn('status', [TruckBooking::STATUS_NEW, TruckBooking::STATUS_ORDER])
            ->whereIn('user_id', $userIds)
            ->withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with('user', 'driver')
            ->count();

            $processingOrders = TruckBooking::filter($filter)
            ->whereIn('status', [
                TruckBooking::STATUS_WAITING,
                TruckBooking::STATUS_ACCEPTED,
                TruckBooking::STATUS_ARRIVED,
                TruckBooking::STATUS_PROCESSING,
                TruckBooking::STATUS_PAUSE,
                TruckBooking::STATUS_CONFIRMATION
            ])
            ->whereIn('user_id', $userIds)
            ->withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with('user', 'driver')
            ->count();

            $doneOrders = TruckBooking::filter($filter)
            ->whereIn('status', [TruckBooking::STATUS_DONE])
            ->whereIn('user_id', $userIds)
            ->withCarType()
            ->withCargoType()
            ->withLoadType()
            ->with('user', 'driver')
            ->count();
        }

        return view('admin2.welcome', [
            'company' => $company,
            'user' => $company->user,
            'bookings_count' => $bookingsCount,
            'waitingOrders' => $waitingOrders,
            'processingOrders' => $processingOrders,
            'doneOrders' => $doneOrders,
            'companyBookingStats' => $companyBookingStats,
			'userscount' => $userscount,
			'is_external' => $company->user->is_external,
        ]);
    }

    public function company(Request $request)
    {
        $company = $request->input('_company');
        $company->loadCount('users');
		/********************/
		$user = auth()->user();
        $userscount = User::leftJoin("user_profiles","users.id","=","user_profiles.user_id")->with('profile')->where("user_profiles.company_id",$company->id)->count();
		/********************/
        return view('admin2.company', [
            'company' => $company,
            'user' => $company->user,
            'companyBookingStats' => $this->loadCompanyBookingStats($company),
            'userscount' => $userscount,
			'is_external' => $company->user->is_external,
        ]);
    }
}
