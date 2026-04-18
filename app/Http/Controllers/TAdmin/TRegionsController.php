<?php


namespace App\Http\Controllers\TAdmin;


use App\Domain\Regions\Models\Region;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Cars\Models\Car;
use App\Domain\Cars\Filter\CarFilter;

class TRegionsController extends Controller
{
    // public function index()
    // {
    //     $regions = Region::paginate();
    //     return view('admin.regions.index', ['regions' => $regions]);
    // }

    public function tracking(Request $request)
    {
        $company = $request->input('_company');
        $request->merge(['company_id' => $company->id]);
        $filter = new CarFilter($request);
        $activeStatuses = [
            TruckBooking::STATUS_ACCEPTED,
            TruckBooking::STATUS_ARRIVED,
            TruckBooking::STATUS_PROCESSING,
            TruckBooking::STATUS_PAUSE,
        ];
        $cars = Car::filter($filter)
            ->with(['user.profile'])
            ->withCount([
                'truck_bookings as active_orders_count' => function ($query) use ($activeStatuses) {
                    $query->whereIn('status', $activeStatuses);
                }
            ]);


        $hasOrders = $request->input('has_order', 'all');
        $phoneNumber = $request->input('driver_phone');
        $carNumber = $request->input('car_number');
        $carTypeId = $request->input('car_type_id');

        $filters = [
            'has_order' => $hasOrders,
            'driver_phone' => $phoneNumber,
            'car_number' => $carNumber,
            'car_type_id' => $carTypeId,
        ];

        // $query = User::whereHas('car');

        if ($phoneNumber) {
            $cars->whereHas('user', function($q) use ($phoneNumber){
                $q->where('username', $phoneNumber);
            });
        }
        if ($carNumber) {
            $cars->where('number', 'like', '%'.$carNumber.'%');
        }

        if ($carTypeId) {
            $cars->where('car_type_id',  $carTypeId);
        }

        if ($hasOrders === 'no_order') {
            $cars->whereDoesntHave('truck_bookings', function ($query) use ($activeStatuses) {
                $query->whereIn('status', $activeStatuses);
            });
        } elseif ($hasOrders === 'has_order') {
            $cars->whereHas('truck_bookings', function ($query) use ($activeStatuses) {
                $query->whereIn('status', $activeStatuses);
            });
        }

        
        $center = [41.326681, 69.244031];
        $onlineUsers = $cars->get();
        foreach ($onlineUsers as $item) {
            if ($item->user->profile && $item->user->profile->lat && $item->user->profile->lng) {
                $center = [$item->user->profile->lat, $item->user->profile->lng];
                break;
            }
        }


        return view('admin2.tracking.index', [
            'carTypes' => CarType::query()
                ->withTranslation()
                ->orderBy('priority')
                ->get(),
            'onlineUsers' => $onlineUsers,
            'filters' => $filters,
            'center' => $center,
        ]);
    }

    // public function edit(Region $region)
    // {
    //     return view('admin.regions.edit', ['region' => $region]);
    // }

    // public function update(Region $region, Request $request)
    // {
    //     $carTypes = $request->input('car_types', []);
    //     $region->carTypes()->detach();
    //     $region->carTypes()->attach($carTypes);
    //     try {
    //         return redirect()->route('admin.regions.edit', $region->id)->with('success', trans('admin.update_success'));
    //     } catch (\Exception $exception) {
    //         return redirect()->route('admin.regions.index')->with('danger', trans('admin.update_failed'));
    //     }
    // }
}
