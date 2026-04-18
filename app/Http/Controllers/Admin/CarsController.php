<?php


namespace App\Http\Controllers\Admin;


use App\Domain\Cars\Filter\CarFilter;
use App\Domain\Cars\Jobs\UpdateCarJob;
use App\Domain\Cars\Models\Car;
use App\Domain\Cars\Requests\StoreCarRequest;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\Companies\Jobs\StoreCompanyJob;
use App\Domain\Companies\Models\Company;
use App\Domain\PushNotifications\Jobs\SendPushJob;
use App\Domain\PushNotifications\Models\PushNotification;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Exports\DriversExport;
use App\Domain\Users\Jobs\AdminStoreUserJob;
use App\Domain\Users\Models\User;
use App\Domain\Users\Requests\AdminStoreUserRequest;
use App\Domain\Users\Requests\AdminUpdateUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CarsController extends Controller
{
    public function index(Request $request)
    {
        $filter = new CarFilter($request);
        $cars = Car::filter($filter)->with('user')->paginateFilter();
        $carTypes = CarType::get();

        return view('admin.cars.index', [
            'cars' => $cars,
            'car_types' => $carTypes,
            'filters' => $filter->filters(),
        ]);
    }
    public function export(Request $request)
    {
        $filter = new CarFilter($request);
        $cars = Car::filter($filter)->with('user')->get();

        $exportData = collect();
        $exportData->push([
            'ID',
            'Ф.И.О',
            'Компания',
            'Тип транспорт',
            'Рейтинг',
            'Дата регистрации',
            'Заказов',
            'Сумма заказов',
            'Отчислений',
            'Модерация',
            'Статус',
        ]);

        foreach ($cars as $car) {
            $driverData = [
                $car->user->id ?? $car->id,
                ($car->user->profile->surname ?? '').' '.($car->user->profile->name ?? '').' '.($car->user->profile->middle_name ?? ''),
                ($car->user && $car->user->profile && $car->user->profile->company) ? $car->user->profile->company->title: 'Cамозанятый',
                $car->carType ? $car->carType->title: '',
                ($car->user && $car->user->profile) ? $car->user->profile->rating ?? '': '',
                $car->created_at->format('d.m.Y'),
                $car->user ? (TruckBooking::where('driver_id', $car->user->id)->count() ?? 0) : 0,
                $car->user ? (TruckBooking::where('driver_id', $car->user->id)->where('status', 'done')->sum('price')  ?? 0) : 0,
                $car->user ? (TruckBooking::where('driver_id', $car->user->id)->where('status', 'done')->sum('commission') ?? 0) : 0,
                $car->moderated ? 'Пройдена': 'Не пройдена',
                $car->active ? trans('admin.active'): trans('admin.not_active'),
            ];
            $exportData->push($driverData);
        }

        $export = new DriversExport($exportData);

        return Excel::download($export, config('app.name').' - drivers '.(date('Y-m-d')).'.xlsx');
    }

    public function debts(Request $request)
    {
        $cars = Car::whereHas('user', function ($query)  {
            $query->whereHas('profile', function ($query)  {
                $query->where('balance', '<', 0);
            });
        })->with('user')->paginate();

        return view('admin.cars.debts', [
            'cars' => $cars,
        ]);
    }

    public function debtsExport(Request $request)
    {
        $cars = Car::whereHas('user', function ($query)  {
            $query->whereHas('profile', function ($query)  {
                $query->where('balance', '<', 0);
            });
        })->with('user')->get();

        $exportData = collect();
        $exportData->push([
            'ID',
            'Ф.И.О',
            'Тип транспорт',
            'Рейтинг',
            'Дата регистрации',
            'Заказов',
            'Задолженность',
            'Статус',
        ]);

        foreach ($cars as $car) {
            $driverData = [
                $car->user->id ?? $car->id,
                ($car->user->profile->surname ?? '').' '.($car->user->profile->name ?? '').' '.($car->user->profile->middle_name ?? ''),
                $car->carType ? $car->carType->title: '',
                ($car->user && $car->user->profile) ? $car->user->profile->rating ?? '': '',
                $car->created_at->format('d.m.Y'),
                $car->user ? (TruckBooking::where('driver_id', $car->user->id)->count() ?? 0) : 0,
                $car->user->profile->balance ?? 0,
                $car->active ? trans('admin.active'): trans('admin.not_active'),
            ];
            $exportData->push($driverData);
        }

        $export = new DriversExport($exportData);

        return Excel::download($export, config('app.name').' - drivers-debts '.(date('Y-m-d')).'.xlsx');
    }

    public function create()
    {
        return view('admin.cars.create');
    }

    public function store(StoreCarRequest $request, AdminStoreUserRequest $userRequest)
    {
        try {
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
            return redirect()->route('admin.cars.show', $car)->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.store_failed'));
        }
    }

    public function edit(Car $car)
    {
        return view('admin.cars.edit', [
            'car' => $car,
            'user' => $car->user,
        ]);
    }

    public function show(Car $car)
    {
        return view('admin.cars.show', [
            'car' => $car,
            'user' => $car->user,
            'notifications' => $car->user->pushNotifications()->orderBy('created_at', 'desc')->paginate(),
        ]);
    }

    public function update(Car $car, StoreCarRequest $request, AdminUpdateUserRequest $userRequest)
    {
        try {
            $userRequest->merge(['role' => null]);
            $user = $this->dispatchSync(new AdminStoreUserJob($userRequest));
            $car = $this->dispatchSync(new UpdateCarJob($request, $user));
            return redirect()->route('admin.cars.show', $car)->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(Car $car)
    {
        try {
            if (isset($car->user) || isset($car->user->profile)) {
                $car->user->profile->company_id = null;
                $car->user->profile->save();
            }
            $car->delete();
            return redirect()->route('admin.cars.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.cars.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
    
    public function destroyLicence(Car $car)
    {
        try {
            if (isset($car->user->profile)) {
                $car->user->profile->deleteLicence();
            }
            return redirect()->route('admin.cars.show', $car)->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            report($exception);
            return redirect()->route('admin.cars.show', $car)->with('danger', trans('admin.destroy_failed'));
        }
    }
    
    public function destroyCarLicence(Car $car)
    {
        try {
            if (isset($car->user->profile)) {
                $car->user->profile->deleteCarLicence();
            }
            return redirect()->route('admin.cars.show', $car)->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.cars.show', $car)->with('danger', trans('admin.destroy_failed'));
        }
    }

    public function sendPush(Request $request)
    {
        try {
            $this->dispatchSync(new SendPushJob($request,'drivers'));
            if($request->filled('user_id')) {
                $user = User::find($request->input('user_id'));
                $car = Car::where('user_id', $user->id)->first();
                if($car)
                {
                    return redirect()->route('admin.cars.show', $car)->with('success', 'Пуш-уведомление отправлено');
                } else {
                    return redirect()->route('admin.users.show', $user)->with('success', 'Пуш-уведомление отправлено');
                }
            } else {
                return redirect()->route('admin.cars.index')->with('success', 'Пуш-уведомление отправлено');
            }
        } catch (\Exception $exception) {
            if($request->filled('user_id')) {
                $user = User::find($request->input('user_id'));
                $car = Car::where('user_id', $user->id)->first();
                if($car)
                {
                    return redirect()->route('admin.cars.show', $car)->with('danger', 'Не удалось отправить уведомление');
                } else {
                    return redirect()->route('admin.users.show', $user)->with('danger', 'Не удалось отправить уведомление');
                }
            } else {
                return redirect()->route('admin.cars.index')->with('danger', 'Не удалось отправить уведомление');
            }
        }
    }

    public function destroyPush(User $user, PushNotification $notification)
    {
        try {
            $notification->delete();

            $car = Car::where('user_id', $user->id)->first();
            if($car)
            {
                return redirect()->route('admin.cars.show', $car)->with('success', trans('admin.destroy_success'));
            } else {
                return redirect()->route('admin.users.show', $user)->with('success', trans('admin.destroy_success'));
            }
        } catch (\Exception $exception) {
            $car = Car::where('user_id', $user->id)->first();
            if($car)
            {
                return redirect()->route('admin.cars.show', $car)->with('danger', trans('admin.destroy_failed'));
            } else {
                return redirect()->route('admin.users.show', $user)->with('danger', trans('admin.destroy_failed'));
            }
        }
    }
}
