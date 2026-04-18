<?php

namespace App\Http\Controllers\Api;


use App\Domain\Cars\Jobs\UpdateCarJob;
use App\Domain\Cars\Models\Car;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\PushNotifications\Models\PushNotification;
use App\Domain\Cars\Requests\UpdateCarRequest;
use App\Domain\Cars\Resources\CarResource;
use App\Domain\OctoTransactions\OctoTransactionRequest;
use App\Domain\PushNotifications\Resources\PushNotificationResource;
use App\Domain\Tcars\Jobs\UpdateTcarJob;
use App\Domain\Tcars\Models\Tcar;
use App\Domain\Tcars\Requests\UpdateTcarRequest;
use App\Domain\Tcars\Resources\TcarResource;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\TruckBookings\Resources\TruckBookingResource;
use App\Domain\Users\Jobs\ApiChangeProfileJob;
use App\Domain\Users\Jobs\UpdateLocationJob;
use App\Domain\Users\Jobs\UpdateLocationHeadlessJob;
use App\Domain\UserDeleteRequests\Jobs\ApiRequestDeleteProfileJob;
use App\Domain\Users\Requests\ApiChangeProfileRequest;
use App\Domain\UserDeleteRequests\Requests\ApiUserDeleteRequest;
use App\Domain\UserDeleteRequests\Model\UserDeleteRequest;
use App\Domain\Users\Requests\UpdateLocationRequest;
use App\Domain\Users\Requests\UpdateLocationHeadlessRequest;
use App\Domain\Users\Resources\UserProfileResource;
use App\Http\Controllers\Controller;
use App\Services\OctoService\OctoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Domain\Users\Jobs\UploadDriverDocJob;
use App\Domain\AdminMessages\Models\AdminMessage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->input('authUser');
        return UserProfileResource::make($user->profile()->with('user')->first());
    }

    public function getOrdersCount(Request $request){
        try {
            $user = $request->input('authUser');
            $new = TruckBooking::where('user_id', $user->id)->where('status', TruckBooking::STATUS_ORDER)->count();
            $processing = TruckBooking::where('user_id', $user->id)->whereNotIn('status', [TruckBooking::STATUS_DONE, TruckBooking::STATUS_ORDER, TruckBooking::STATUS_CANCELED])->count();
            $done = TruckBooking::where('user_id', $user->id)->where('status', TruckBooking::STATUS_DONE)->count();
            
            return response()->json([
                'new' => $new,
                'processing' => $processing,
                'done' => $done,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error getting orders count'], 500);
        }
    }

    public function update(ApiChangeProfileRequest $request)
    {
        $user = $request->input('authUser');
        try {
            $userProfile = $this->dispatchSync(new ApiChangeProfileJob($request, $user->profile));
            return UserProfileResource::make($userProfile);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }
    
    public function driverUploadDocs(Request $request)
    {
        $user = $request->input('authUser');
        try {
            $this->dispatchSync(new UploadDriverDocJob($request, $user));
//          if(!$user->car){
            if(!$request->has('car_type_id') || empty($request->input('car_type_id'))) {
                $request->merge(['car_type_id' => CarType::first()->id]);
            }
            $car = $this->dispatchSync(new UpdateCarJob($request, $user));
            if ($car) {
                $car->load('carType.translations');
            }

//          }

            return response()->json(['message' => 'ok'], 200);
        } catch (\Exception $exception) {
            \Log::error($exception);
            return response()->json($exception, 500);
        }
    }
    
    public function updateLocale(Request $request)
    {
        $user = $request->input('authUser');
        try {
            $profile = $user->profile;
            $language = $request->header('Accept-Language', \App::getLocale());
            $profile->language = $language;
            $user->save();
            $user->profile->save();
            return UserProfileResource::make($profile);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    public function deleteProfileRequest(ApiUserDeleteRequest $request){
        $user = $request->input('authUser');
        try {
            $reqCount = UserDeleteRequest::where('user_id', $user->id)->count();
            if($reqCount < 1){
                $this->dispatchSync(new ApiRequestDeleteProfileJob($request, $user));
                return response()->json(["message" => 'success'], 200);
            } else {
                return response()->json(["message" => 'We have already received your request. Our Customer support Team will reach out to you soon!'], 403);
            }
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    public function updateLocation(UpdateLocationRequest $request)
    {
        $user = $request->input('authUser');
        try {
            $userProfile = $this->dispatchSync(new UpdateLocationJob($request, $user->profile));
            return UserProfileResource::make($userProfile);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    public function updateLocationHeadless(UpdateLocationHeadlessRequest $request)
    {
        $user = $request->input('authUser');

        try {
            // \Log::info("Location before: ");
            // \Log::info($user->profile->lat);
            // \Log::info($user->profile->lng);
            
            
            $userProfile = $this->dispatchSync(new UpdateLocationHeadlessJob($request, $user->profile));
            // \Log::info('---------------------------------------------');
            // \Log::info('updating user location: ');
            // \Log::info($user);
            // \Log::info($userProfile->name);
            // \Log::info($userProfile->surname);
            // \Log::info($userProfile->lat);
            // \Log::info($userProfile->lng);
            // \Log::info($request->input('location', null));
            // \Log::info('---------------------------------------------');
            return UserProfileResource::make($userProfile);
        } catch (\Exception $exception) {
            \Log::info($exception);
            return response()->json($exception, 500);
        }
    }

    public function showCar(Request $request)
    {
        $car = $request->input('authUser')->car;
        if ($car) {
            $car->load('carType.translations');
        }
        return CarResource::make($car ?? new Car());
    }

    public function updateCar(UpdateCarRequest $request)
    {
        $user = $request->input('authUser');
        try {
            $car = $this->dispatchSync(new UpdateCarJob($request, $user));
            if ($car) {
                $car->load('carType.translations');
            }
            return CarResource::make($car);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    public function showTCar(Request $request)
    {
        $car = $request->input('authUser')->tcar;
        if ($car) {
            $car->load('carType.translations');
        }
        return TcarResource::make($car ?? new Tcar());
    }

    public function updateTCar(UpdateTcarRequest $request)
    {
        $user = $request->input('authUser');
        try {
            $car = $this->dispatchSync(new UpdateTcarJob($request, $user));
            if ($car) {
                $car->load('carType.translations');
            }
            return TcarResource::make($car);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    public function offer(Request $request)
    {
        $user = $request->input('authUser');

        $pdf = \PDF::loadView('documents.offer', [
            'user' => $user->profile()->with('user')->first()
        ]);

        return $pdf->stream('casva-offer.pdf');
    }

    public function statistics(Request $request)
    {
        $user = $request->input('authUser');

        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        if ($dateStart && $dateEnd) {
            $sum = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->where('driver_id', $user->id)->where('status', 'done')->sum('price');
            $commission = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->where('driver_id', $user->id)->where('status', 'done')->sum('commission');
            $distance = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->where('driver_id', $user->id)->where('status', 'done')->sum('distance');
            $count = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->where('driver_id', $user->id)->where('status', 'done')->count();
            $bookings = TruckBooking::whereBetween('created_at', [
                date('Y-m-d 00:00:00', strtotime($dateStart)),
                date('Y-m-d 23:59:59', strtotime($dateEnd)),
            ])->where('driver_id', $user->id)->where('status', 'done')->orderByDesc('created_at')->get();
        } elseif ($dateStart) {
            $sum = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->where('driver_id', $user->id)->where('status', 'done')->sum('price');
            $commission = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->where('driver_id', $user->id)->where('status', 'done')->sum('commission');
            $distance = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->where('driver_id', $user->id)->where('status', 'done')->sum('distance');
            $count = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->where('driver_id', $user->id)->where('status', 'done')->count();
            $bookings = TruckBooking::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($dateStart)))->where('driver_id', $user->id->where('status', 'done'))->orderByDesc('created_at')->get();
        }
        else {
            $sum = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->where('driver_id', $user->id)->where('status', 'done')->sum('price');
            $commission = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->where('driver_id', $user->id)->where('status', 'done')->sum('commission');
            $distance = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->where('driver_id', $user->id)->where('status', 'done')->sum('distance');
            $count = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->where('driver_id', $user->id)->where('status', 'done')->count();
            $bookings = TruckBooking::where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($dateEnd)))->where('driver_id', $user->id)->where('status', 'done')->orderByDesc('created_at')->get();
        }

        $bookingsStatus = $request->input('bookings', 'on');

        return response()->json([
            'data' => [
                'sum' => $sum - $commission,
                'tax_sum' => $commission,
                'distance' => $distance,
                'count' => $count,
                'bookings' => $bookingsStatus == 'on' ? TruckBookingResource::collection($bookings): TruckBookingResource::collection(collect([])),
            ]
        ]);
    }

    public function notifications(Request $request)
    {
        $user = $request->input('authUser');
        $notifications = PushNotification::where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate();
        return PushNotificationResource::collection($notifications);
    }

    public function notificationsCount(Request $request)
    {
        $user = $request->input('authUser');
        $notifications = PushNotification::where(['user_id' => $user->id, 'read' => 0])->count();
        return response()->json(['data' => ['count' => $notifications]]);
    }
    
    public function messagesCount(Request $request)
    {
        $user = $request->input('authUser');
        $count = AdminMessage::where(['chat_id' => $user->id, 'read' => 0])->count();
        return response()->json(['data' => ['count' => $count]]);
    }

    public function notificationsMarkRead(Request $request)
    {
        $user = $request->input('authUser');
        $notifications = PushNotification::where('user_id', $user->id)->where('read', 0);
        $notifications->update(['read' => 1]);
        return response()->json(['data' => ['ok' => true]]);
    }
    
    public function deleteNotification(Request $request){
        $id =   $request->input('id');
        $deleted = PushNotification::find($id)->delete();
        if($deleted) return response()->json(['data' => ['ok' => true]], 200);
        return response()->json(['data' => ['ok' => false]], 400);
    }
    
    public function deleteAllNotifications(Request $request){
        $user = $request->input('authUser');
        $deletedAll = PushNotification::where('user_id', $user->id)->delete();
        if($deletedAll) return response()->json(['data' => ['ok' => true]], 200);
        return response()->json(['data' => ['ok' => false]], 400);
    }

    public function getPaymentLink(OctoTransactionRequest $request)
    {
        $user = $request->input('authUser');
        $octoService = new OctoService();
        return $octoService->prepare($user->id, $request->toArray());
    }
}
