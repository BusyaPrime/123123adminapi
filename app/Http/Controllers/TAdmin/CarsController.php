<?php

namespace App\Http\Controllers\TAdmin;

use App\Domain\Cars\Filter\CarFilter;
use App\Domain\Cars\Jobs\UpdateCarJob;
use App\Domain\Cars\Models\Car;
use App\Domain\Cars\Requests\StoreCarRequest;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\Users\Jobs\AdminStoreUserJob;
use App\Domain\Users\Requests\AdminStoreUserRequest;
use App\Domain\Users\Requests\AdminUpdateUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarsController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->input('_company');
        $request->merge(['company_id' => $company->id]);
        $filter = new CarFilter($request);
        $cars = Car::filter($filter)
            ->with(['user.profile.company', 'carType'])
            ->withCount(['truck_bookings as orders_count'])
            ->withSum([
                'truck_bookings as done_price_sum' => function ($query) {
                    $query->where('status', 'done');
                }
            ], 'price')
            ->withSum([
                'truck_bookings as done_commission_sum' => function ($query) {
                    $query->where('status', 'done');
                }
            ], 'commission')
            ->paginateFilter();
        $carTypes = CarType::get();

        return view('admin2.cars.index', [
            'cars' => $cars,
            'car_types' => $carTypes,
            'filters' => $filter->filters(),
        ]);
    }

    public function create(Request $request)
    {
        $company = $request->input('_company');
        return view('admin2.cars.create', [
            'company' => $company
        ]);
    }

    public function store(StoreCarRequest $request, AdminStoreUserRequest $userRequest)
    {
        try {
            $company = $request->input('_company');
            $userRequest->merge(['company_id' => $company->id]);
            $userRequest->merge(['role' => null]);
            $user = $this->dispatchSync(new AdminStoreUserJob($userRequest));
            $existsCar = Car::where('user_id', $user->id)->first();
            if($existsCar) {
                if (isset($user) || isset($user->profile)) {
                    $user->profile->company_id = null;
                    $user->profile->save();
                }
                return redirect()->back()->with('danger', 'Этот номер уже используется другим водителем');
            }
            $request->merge(['active' => $userRequest->input('active')]);
            $car = $this->dispatchSync(new UpdateCarJob($request, $user));
            return redirect()->route('admin2.cars.show', $car)->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.store_failed'));
        }
    }

    public function edit(Car $car, Request $request)
    {
        $company = $request->input('_company');
        if (!isset($car->user) || !isset($car->user->profile) || $car->user->profile->company_id != $company->id) {
            abort(404);
        }
        return view('admin2.cars.edit', [
            'car' => $car,
            'user' => $car->user,
            'company' => $company
        ]);
    }

    public function show(Car $car, Request $request)
    {
        $company = $request->input('_company');
        if (!isset($car->user) || !isset($car->user->profile) || $car->user->profile->company_id != $company->id) {
            abort(404);
        }
        $car = Car::query()
            ->with(['user.profile.region', 'carType', 'loadTypes'])
            ->withCount([
                'truck_bookings as orders_count',
                'truck_bookings as done_orders_count' => function ($query) {
                    $query->where('status', 'done');
                },
                'truck_bookings as canceled_orders_count' => function ($query) {
                    $query->where('status', 'canceled');
                },
            ])
            ->withSum([
                'truck_bookings as done_price_sum' => function ($query) {
                    $query->where('status', 'done');
                }
            ], 'price')
            ->withSum([
                'truck_bookings as done_commission_sum' => function ($query) {
                    $query->where('status', 'done');
                }
            ], 'commission')
            ->findOrFail($car->id);

        return view('admin2.cars.show', [
            'car' => $car,
            'user' => $car->user,
            'company' => $company,
            'notifications' => $car->user->pushNotifications()->orderBy('created_at', 'desc')->paginate(),
        ]);
    }

    public function update(Car $car, StoreCarRequest $request, AdminUpdateUserRequest $userRequest)
    {
        try {
            $company = $request->input('_company');
            $userRequest->merge(['company_id' => $company->id]);
            $userRequest->merge(['role' => null]);
            $user = $this->dispatchSync(new AdminStoreUserJob($userRequest));
            $car = $this->dispatchSync(new UpdateCarJob($request, $user));
            return redirect()->route('admin2.cars.show', $car)->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(Car $car, Request $request)
    {
        $company = $request->input('_company');
        if (!isset($car->user) || !isset($car->user->profile) || $car->user->profile->company_id != $company->id) {
            abort(404);
        }
        try {
            if (isset($car->user) || isset($car->user->profile)) {
                $car->user->profile->company_id = null;
                $car->user->profile->save();
            }
            $car->delete();
            return redirect()->route('admin2.cars.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin2.cars.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
