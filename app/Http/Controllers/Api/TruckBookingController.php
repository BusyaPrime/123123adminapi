<?php

namespace App\Http\Controllers\Api;


use App\Domain\Cars\Models\Car;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\CommissionRate\Models\CommissionRate;
use App\Domain\Companies\Models\Company;
use App\Domain\Messages\Jobs\SendMessageJob;
use App\Domain\Messages\Models\Message;
use App\Domain\Messages\Requests\SendMessageRequest;
use App\Domain\Messages\Resuorces\MessageResource;
use App\Domain\PushNotifications\Jobs\SendPushJob;
use App\Domain\PushNotifications\Jobs\SendDelayedPushJob;
use App\Domain\Transactions\Models\Transaction;
use App\Domain\TruckBookings\Exceptions\CompanyNotFoundForPaymentException;
use App\Domain\TruckBookings\Jobs\TruckBookingDriverReviewJob;
use App\Domain\TruckBookings\Jobs\TruckBookingJob;
use App\Domain\TruckBookings\Jobs\TruckBookingEditJob;
use App\Domain\TruckBookings\Jobs\TruckBookingViewJob;
use App\Domain\TruckBookings\Jobs\TruckBookingReviewJob;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\TruckBookings\Models\TruckBookingView;
use App\Domain\TruckBookings\Requests\TruckBookingRequest;
use App\Domain\TruckBookings\Requests\TruckBookingReviewRequest;
use App\Domain\TruckBookings\Requests\TruckBookingStatusRequest;
use App\Domain\TruckBookings\Resources\TruckBookingResource;
use App\Domain\Users\Models\User;
use App\Events\DriverOrderlistUpdatedEvent;
use App\Events\OrderListUpdatedEvent;
use App\Events\OrderStatusEvent;
use App\Events\SearchStartEvent;
use App\Events\StartOrderListUpdatingEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\TruckBookings\Jobs\CalculateBookingPriceJob;
use App\Domain\TruckBookings\Jobs\InternalOrderJob;
use App\Domain\TruckBookings\Models\BookingPriceOffer;

class TruckBookingController extends Controller
{
    protected function resolveBookingPaymentType(Request $request, ?TruckBooking $truckBooking = null): string
    {
        if ($request->filled('payment_type')) {
            return $request->input('payment_type');
        }

        if ($truckBooking && $truckBooking->payment_type) {
            return $truckBooking->payment_type;
        }

        return TruckBooking::PAY_CASH;
    }

    protected function resolveClientCompanyFromRequest(Request $request, array $with = []): ?Company
    {
        $company = $request->input('clientCompany');

        if ($company instanceof Company) {
            if (empty($with)) {
                return $company;
            }

            return Company::with($with)->find($company->id);
        }

        $user = $request->input('authUser');

        if ($user instanceof User) {
            return $user->resolveClientCompany($with);
        }

        return null;
    }

    protected function resolveCompanyPayment(User $user, string $paymentType): ?Company
    {
        if ($paymentType !== TruckBooking::PAY_COMPANY) {
            return null;
        }

        $company = $user->resolveClientCompany(['user', 'priority']);

        if (!$company instanceof Company) {
            throw new CompanyNotFoundForPaymentException();
        }

        return $company;
    }

    protected function validateCompanyPayment(User $user, string $paymentType)
    {
        $company = $this->resolveCompanyPayment($user, $paymentType);

        if (!$company instanceof Company) {
            return null;
        }

        if ($company->user && $company->user->active == 0) {
            return response()->json(['message' => 'company_banned'], 400);
        }

        $companyBalanceLimit = $company->priority ? (int) $company->priority->quantity : 0;

        if ($companyBalanceLimit <= 0) {
            return null;
        }

        $companyBalanceWithLimit = (int) $company->balance + $companyBalanceLimit;
        $ordersPriceSum = (int) TruckBooking::where('client_company_id', $company->id)
            ->whereNotIn('status', [TruckBooking::STATUS_CANCELED, TruckBooking::STATUS_DONE])
            ->sum('price');

        if ($ordersPriceSum > $companyBalanceWithLimit) {
            return response()->json(['message' => 'unsufficient balance'], 422);
        }

        return null;
    }

	public function getBookingsInOrder(Request $request)
	{
		$user_id = $request->input('authUser')->id;
		$car = Car::where('user_id', $user_id)->first();

		if (!$car) {
			return response()->json(['data' => []]);
		}

		$myOrders = $request->input('my_orders');
		$regionFromId = $request->input('region_from_id');
		$regionToId = $request->input('region_to_id');
		$dateFrom = $request->input('date_from', date('Y-m-d')) . ' 00:00:00';
		$dateTo = $request->input('date_to', date('Y-m-d')) . ' 23:59:59';
		$query = $car->bookings();

		if ($myOrders) {
			$car_type_id_array = array();
			$carget = Car::withCarType()->where('user_id', '=', $user_id)->first();
			$carType = CarType::where('id', '=', $carget->car_type_id)->first();
			$driverCarWeight = $carType->max_weight;
			$carTypeNext = CarType::where('max_weight', '<=', $driverCarWeight)->orderBy('max_weight', 'DESC')->limit(
				2
			)->get();
			foreach ($carTypeNext as $k => $Val) {
				array_push($car_type_id_array, $Val->id);
			}

			if (count($car_type_id_array) > 0) {
				$query = TruckBooking::where('status', TruckBooking::STATUS_ORDER)
					->where('pickup_date', '>=', date('Y-m-d'))->whereIn('car_type_id', $car_type_id_array);
			} else {
				$query = $car->bookings()
					->where('status', TruckBooking::STATUS_ORDER)
					->where('pickup_date', '>=', date('Y-m-d'));
			}
		} else {
			$query = TruckBooking::where('status', TruckBooking::STATUS_ORDER)
				->where('pickup_date', '>=', date('Y-m-d'));
		}

		if ($regionFromId && $regionToId) {
			$query
				->where('region_from_id', $regionFromId)
				->where('region_to_id', $regionToId);
		} elseif ($regionFromId) {
			$query
				->where('region_from_id', $regionFromId);
		} elseif ($regionToId) {
			$query
				->where('region_to_id', $regionToId);
		}

		if ($dateFrom && $dateTo) {
			$query
				->whereBetween('pickup_date', [$dateFrom, $dateTo]);
		} elseif ($dateFrom) {
			$query
				->where('pickup_date', '>=', $dateFrom);
		} elseif ($dateTo) {
			$query
				->where('pickup_date', '<=', $dateTo);
		}

		/************For Partial Bookings**************/
		if ($myOrders) {
			$getongoingorders = $car->bookings()
				->withCarType()
				->withCargoType()
				->withLoadType()
				->with('user', 'driver')
				->orderBy('created_at', 'DESC')
				->whereNotIn('status', [TruckBooking::STATUS_DONE, TruckBooking::STATUS_DONE])->where(
					'driver_id',
					$user_id
				)->get();
		} else {
			$getongoingorders = TruckBooking::withCarType()
				->withCargoType()
				->withLoadType()
				->with('user', 'driver')
				->orderBy('created_at', 'DESC')->whereNotIn('status', [TruckBooking::STATUS_DONE])->where(
					'driver_id',
					$user_id
				)->get();
		}

		$bookings = $query
			->withCarType()
			->withCargoType()
			->withLoadType()
			->with('user', 'driver')
			->with([
				'bookingPriceOffer' => function ($query) use ($user_id) {
					$query->where('driver_id', $user_id)->orderByDesc('created_at')->limit(1);
				}
			])
			->orderBy('created_at', 'DESC')
			->paginate(TruckBooking::COUNT_ON_PAGE);

		return TruckBookingResource::collection($bookings);
	}


	public function getCompletedOrders(Request $request)
	{
		$user_id = $request->input('authUser')->id;
		$car = Car::where('user_id', $user_id)->first();
		if (!$car) {
			return response()->json(['data' => []]);
		}

		$regionFromId = $request->input('region_from_id');
		$regionToId = $request->input('region_to_id');

		// $dateFrom = $request->input('date_from', date('Y-m-d')).' 00:00:00';
		// $dateTo = $request->input('date_to', date('Y-m-d')).' 23:59:59';

		$dateFrom = null;
		$dateTo = null;

		$query = $car->bookings()
			->where('status', TruckBooking::STATUS_CANCELED)
			->where('status', TruckBooking::STATUS_DONE);
//            ->where('pickup_date', '>=', date('Y-m-d'));

		if ($regionFromId && $regionToId) {
			$query
				->where('region_from_id', $regionFromId)
				->where('region_to_id', $regionToId);
		} elseif ($regionFromId) {
			$query
				->where('region_from_id', $regionFromId);
		} elseif ($regionToId) {
			$query
				->where('region_to_id', $regionToId);
		}

		if ($dateFrom && $dateTo) {
			$query
				->whereBetween('pickup_date', [$dateFrom, $dateTo]);
		} elseif ($dateFrom) {
			$query
				->where('pickup_date', '>=', $dateFrom);
		} elseif ($dateTo) {
			$query
				->where('pickup_date', '<=', $dateTo);
		}
		$bookings = $query
//            ->withCarType()
			//          ->withCargoType()
			//        ->withLoadType()
			->with('user', 'driver')
			->paginate(TruckBooking::COUNT_ON_PAGE);
		//      ->orderBy('created_at', 'DESC')->get();
		return TruckBookingResource::collection($bookings);
	}

	public function testorder(TruckBookingRequest $request)
	{
		try {
            $companyPaymentValidation = $this->validateCompanyPayment(
                $request->input('authUser'),
                $this->resolveBookingPaymentType($request)
            );

            if ($companyPaymentValidation) {
                return $companyPaymentValidation;
            }

            $carType = CarType::findOrFail($request->input('car_type_id'));
            $carType = $this->dispatchSync(new CalculateBookingPriceJob($request, $carType));
			$savedBooking = $this->dispatchSync(new TruckBookingJob($request, $carType));
			$booking = TruckBooking::withCarType()
				->withCargoType()
				->withLoadType()
				->with('user', 'driver')->find($savedBooking->id);
//            if($request->input('instant')) {
			event(new SearchStartEvent($booking));
//            }

			$bookingResource = TruckBookingResource::make($booking);
			$data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];

			$cars = $booking->cars;

			foreach ($cars as $car) {
				$request->merge(['user_id' => $car->user_id]);
				$request->merge(['title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id]]);
				$request->merge(['message' => ['message' => 'messages.Доступен новый заказ', 'arg' => '']]);
				$this->dispatchSync(new SendPushJob($request, 'drivers', false, $data));
			}

			return TruckBookingResource::make($booking);
        } catch (CompanyNotFoundForPaymentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
		} catch (\Exception $exception) {
			return response()->json($exception, 500);
		}
	}

	public function book(TruckBookingRequest $request)
	{
		try {
			$user = $request->input('authUser');
			$companyPaymentValidation = $this->validateCompanyPayment(
                $user,
                $this->resolveBookingPaymentType($request)
            );

			if ($companyPaymentValidation) {
				return $companyPaymentValidation;
			}

			$carTypeId = $request->input('car_type_id');
			$carType = CarType::findOrFail($carTypeId);

				$carType = $this->dispatchSync(new CalculateBookingPriceJob($request, $carType));
				if ($carType->calculatedPrice == 0) {
					return response()->json(['message' => 'error putting an order'], 500);
				}

			$savedBooking = $this->dispatchSync(new TruckBookingJob($request, $carType));
			$booking = TruckBooking::withCarType()
				->withCargoType()
				->withLoadType()
				->with('user', 'driver')->find($savedBooking->id);

			event(new SearchStartEvent($booking));

			\Log::info('------------------------COMPANY-----------------');

			if ($booking->client_company_id !== null) {
				$companyId = (int) $booking->client_company_id;
				\Log::info("------------------------COMPANY_ID: $companyId-----------------");
				if ($companyId == 47) {
					$booking->driver_id = 414;
					$booking->status = TruckBooking::STATUS_ACCEPTED;
					$booking->save();
					// $this->dispatchSync(new InternalOrderJob($booking->id));
					// InternalOrderJob::dispatch($booking->id)->delay(now()->addSeconds(4));
				}
			}

			$bookingResource = TruckBookingResource::make($booking);
			$data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];

			$cars = Car::where('active', 1)->where('car_type_id', $booking->car_type_id)->get();

			foreach ($cars as $car) {
				$request->merge([
					'user_id' => $car->user_id,
					'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
					'message' => ['message' => 'messages.Доступен новый заказ', 'arg' => ''],
					'notification_type' => 'booking'
				]);
				$this->dispatch(new SendPushJob($request, 'drivers', true, $data));
			}

			return TruckBookingResource::make($booking);
        } catch (CompanyNotFoundForPaymentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
		} catch (\Exception $exception) {
			return response()->json('error_putting_an_order', 500);
		}
	}

	public function bookEdit(TruckBookingRequest $request, TruckBooking $truckBooking)
	{
		try {
			$user = $request->input('authUser');

			if ($truckBooking->status != TruckBooking::STATUS_ORDER) {
				return response(['message' => 'status_doesnt_match'], 422);
			}

			if ($truckBooking->user_id != $user->id) {
				return response(['message' => 'user_doesnt_match'], 422);
			}

            $companyPaymentValidation = $this->validateCompanyPayment(
                $user,
                $this->resolveBookingPaymentType($request, $truckBooking)
            );

            if ($companyPaymentValidation) {
                return $companyPaymentValidation;
            }

			$editBooking = TruckBooking::withCarType()
                ->with('bookingPriceOffer')
                ->with('user', 'driver')
                ->find($truckBooking->id);

            $carType = CarType::findOrFail($request->input('car_type_id', $editBooking->car_type_id));
			$carType = $this->dispatchSync(new CalculateBookingPriceJob($request, $carType));
			$booking = $this->dispatchSync(new TruckBookingEditJob($request, $editBooking, $carType));
			$bookingOffers = BookingPriceOffer::where('booking_id', $booking->id);
			$bookingOffers->update(['active' => 0]);
			$bookingResource = TruckBookingResource::make($booking);

			$data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];
			$cars = Car::where('active', 1)->where('car_type_id', $booking->car_type_id)->get();

			foreach ($cars as $car) {
				$request->merge([
					'user_id' => $car->user_id,
					'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
					'message' => ['message' => 'messages.push_messages.new_order_available_edited', 'arg' => ''],
					'notification_type' => 'booking'
				]);
				$this->dispatchSync(new SendPushJob($request, 'drivers', true, $data));
			}

			event(new SearchStartEvent($booking));

            return $bookingResource;
        } catch (CompanyNotFoundForPaymentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
		} catch (\Exception $exception) {
			return response()->json(['message' => $exception->getMessage()], 500);
		}
	}

	public function driverOfferPrice(Request $request, TruckBooking $truckBooking)
	{
		try {
			if ($truckBooking->status != TruckBooking::STATUS_ORDER) {
				return response()->json(['message' => 'already_accepted'], 422);
			}

			$user = $request->input('authUser');
			$request->merge(['driver_id' => $user->id]);

			$driverOffers = BookingPriceOffer::where('driver_id', $user->id)->where('booking_id', $truckBooking->id);

			if ($driverOffers->count() > 2) { //set to 2
				return response()->json(['message' => trans('messages.push_messages.offers_limit_exceeded')], 422);
			}

			$IsCarValid = 0;
			$car = Car::withCarType()->where('user_id', $user->id)->first();

			if (!$car || $car->moderated == 0) {
				return response()->json(['message' => 'not_moderated'], 422);
			}


			$OngoingOrdersCount = TruckBooking::where('status', '!=', TruckBooking::STATUS_DONE)
				->where('status', '!=', TruckBooking::STATUS_CANCELED)
				->where('driver_id', '=', $user->id)
				->count();

			if ($OngoingOrdersCount > 1) {
				return response()->json(['message' => trans('messages.push_messages.truck_already_have_orders')], 422);
			}


			$carType = CarType::where('id', $car->car_type_id)->first();

			$driverCarWeight = $carType->max_weight;
			$carTypeNext = CarType::where('max_weight', '<=', $driverCarWeight)->orderBy('max_weight', 'DESC')->limit(2)->get();

			foreach ($carTypeNext as $Val) {
				if ($Val->id == $truckBooking->car_type_id) {
					$IsCarValid = 1;
				}
			}

			if ($IsCarValid == 0) {
				return response()->json(['message' => trans('messages.push_messages.another_truck')], 422);
			}

			if ($truckBooking->driver_id) {
				return response()->json([
					'message' => trans('messages.push_messages.booking_accepted')
				], 403);
			}

			if ($driverOffers->count() > 0) {
				$driverOffers->update(['active' => 0]);
			}

			$driverOffer = new BookingPriceOffer($request->input());

			$driverOffer->booking_id = $truckBooking->id;
			
			if($truckBooking->payment_type === TruckBooking::PAY_COMPANY) {
				$driverOffer->client_amount = floor(($driverOffer->amount * (1 + TruckBooking::BANK_TRANSFER_COEFFICIENT)) / 1000) * 1000;
			} else {
				$driverOffer->client_amount = $driverOffer->amount;
			}

			$driverOffer->save();


			//notify owner of booking about new price offer
			$booking = TruckBooking::where('id', $truckBooking->id)
				->withCargoType()
				->withLoadType()
				->with('user', 'driver')
				->with([
					'bookingPriceOffer' => function ($query) use ($user) {
						$query->where('driver_id', $user->id)->orderByDesc('created_at')->limit(1);
					}
				])->first();

			$bookingResource = TruckBookingResource::make($booking);
			$data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];
			$request->merge([
				'user_id' => $booking->user_id,
				'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
				'message' => ['message' => 'messages.push_messages.new_driver_offer', 'arg' => ''],
				'notification_type' => 'booking'
			]);
			$this->dispatchSync(new SendPushJob($request, '', true, $data));

			event(new OrderStatusEvent($booking, $booking->getDriverCar(), $user->id, $booking->id));

			return $bookingResource;
		} catch (\Throwable $th) {
			return response()->json(['message' => 'err'], 500);
		}
	}

	public function clientOfferPrice(Request $request, TruckBooking $booking)
	{
		try {
			$price = $request->input('amount', 0);

			if ($price == 0) {
				return response()->json(['message' => trans('messages.push_messages.equal_to_zero')], 500);
			}

			if ($booking->status != TruckBooking::STATUS_ORDER) {
				return response()->json(['message' => trans('messages.push_messages.order_already_accepted')], 500);
			}

			$booking = TruckBooking::withCarType()
				->withCargoType()
				->withLoadType()
				->with('user', 'driver')->find($booking->id);

			$booking->price = $price;
			
			if($booking->payment_type === TruckBooking::PAY_COMPANY) {
				$booking->driver_price = floor(($price * (1 - TruckBooking::BANK_TRANSFER_COEFFICIENT)) / 1000) * 1000;
			} else {
				$booking->driver_price = $price;
			}

			$booking->save();

			$bookingResource = TruckBookingResource::make($booking);

			$data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];
			$cars = Car::where('active', 1)->where('car_type_id', $booking->car_type_id)->get();

			foreach ($cars as $car) {
				$request->merge([
					'user_id' => $car->user_id,
					'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
					'message' => ['message' => 'messages.push_messages.published_with_new_price', 'arg' => ''],
					'notification_type' => 'booking'
				]);
				$this->dispatchSync(new SendPushJob($request, '', true, $data));
			}

			//notify all drivers about new price of the order
			event(new OrderListUpdatedEvent([], 'trucks.all'));

			return TruckBookingResource::make($booking);
		} catch (\Throwable $th) {
			return response()->json(['message' => 'err'], 500);
		}
	}

	public function clientAcceptDriverOffer(Request $request, TruckBooking $truckBooking)
	{
		try {
			//accept order, save driver_id to the booking notify driver about assigned order...
			$driver_id = $request->input('driver_id');
			$companyPaymentValidation = $this->validateCompanyPayment(
                $request->input('authUser'),
                $truckBooking->payment_type ?? TruckBooking::PAY_CASH
            );

			if ($companyPaymentValidation) {
				return $companyPaymentValidation;
			}

			$driverOffer = BookingPriceOffer::find($request->input('offer_id'));
			if (!$driverOffer) {
				return response()->json(['message' => 'offer_doesnt_exist'], 422);
			}

			if (!$driver_id || $truckBooking->status != TruckBooking::STATUS_ORDER) {
				return response()->json(['message' => 'err'], 500);
			}

			$driverOngoings = TruckBooking::where('driver_id', $driver_id)->whereNotIn(
				'status',
				[TruckBooking::STATUS_DONE, TruckBooking::STATUS_CANCELED, TruckBooking::STATUS_CONFIRMATION]
			)->get();

			if ($driverOngoings->count() > 2) {
				return response()->json(['message' => 'driver_has_many_orders'], 422);
			}

			$truckBooking->cars()->detach();
			$truckBooking->driver_id = $driver_id;
			$truckBooking->status = TruckBooking::STATUS_ACCEPTED;
			$truckBooking->recomended_price = $truckBooking->price;
			$truckBooking->price = $driverOffer->client_amount;
			$truckBooking->driver_price = $driverOffer->amount;
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

			$truckBooking->save();

			event(new DriverOrderlistUpdatedEvent($truckBooking->status, $driver_id));
			event(new OrderStatusEvent($truckBooking, $truckBooking->getDriverCar(), '', $truckBooking->id));

			$otherOffers = BookingPriceOffer::where('driver_id', '<>', $driver_id)
				->where('booking_id', $truckBooking->id)->get();

			//if booking has another offers send to other drivers that the client accepted another driver (you have not been selected)
			foreach ($otherOffers as $_offer) {
				$request->merge([
					'user_id' => $_offer->driver_id,
					'title' => ['message' => 'messages.push_messages.order', 'arg' => $truckBooking->id],
					'message' => ['message' => 'messages.push_messages.client_accepted_another_driver', 'arg' => ''],
				]);
				$this->dispatchSync(new SendPushJob($request, '', true, []));
			}

			$bookingResource = TruckBookingResource::make(
				$truckBooking->withCargoType()->withLoadType()->withCarType()->with('user', 'driver')->find(
					$truckBooking->id
				)
			);

			$data = ['booking_id' => $truckBooking->id, 'booking_info' => $bookingResource];

			$request->merge([
				'user_id' => $driver_id,
				'title' => ['message' => 'messages.push_messages.order', 'arg' => $truckBooking->id],
				'message' => ['message' => 'messages.push_messages.client_confirmed_driver_offer', 'arg' => ''],
				'notification_type' => 'booking'
			]);
			$this->dispatchSync(new SendPushJob($request, '', true, $data));

			return $bookingResource;
        } catch (CompanyNotFoundForPaymentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
		} catch (\Throwable $th) {
			return response()->json(['message' => 'err'], 500);
		}
	}

	public function getBookingOffers(TruckBooking $booking)
	{
		try {
			$driverOffers = BookingPriceOffer::where('booking_id', $booking->id)
				->where('active', 1)
				->with(['driver', 'driver.profile', 'driver.car.carType'])
				->orderBy('amount')
				->limit(5)
				->get();
			return response()->json(['data' => $driverOffers]);
		} catch (\Throwable $th) {
			return response()->json($th, 500);
		}
	}


	public function getOrder(TruckBooking $truckBooking, Request $request)
	{
		$user = $request->input('authUser');

		$bookingQuery = TruckBooking::withCarType()
			->withCargoType()
			->withLoadType()
			->where('id', $truckBooking->id)
			->with('user', 'driver');

		$booking = $bookingQuery->first();
		$company = null;

		if ($request->header('userrole') === 'customer') {
			$company = $this->resolveClientCompanyFromRequest($request);

			if (
				!$booking instanceof TruckBooking ||
				$booking !== null && $company === null && $booking->user_id !== $user->id ||
				$booking->client_company_id !== null && $booking !== null && $company !== null && $booking->client_company_id !== $company->id
			) {
				return response()->json(['message' => 'Order not found'], 404);
			}
		}

		return TruckBookingResource::make($booking);
	}

	public function orders(Request $request)
	{
		$orders = TruckBooking::where('driver_id', $request->input('authUser')->id)
			->where(function ($q) {
				$q->orWhere('status', TruckBooking::STATUS_PROCESSING)
					->orWhere('status', TruckBooking::STATUS_WAITING)
					->orWhere('status', TruckBooking::STATUS_ACCEPTED)
					->orWhere('status', TruckBooking::STATUS_ARRIVED)
					->orWhere('status', TruckBooking::STATUS_PAUSE)
					->orWhere('status', TruckBooking::STATUS_CONFIRMATION);
			})
			->withCarType()
			->withCargoType()
			->withLoadType()
			->with('user', 'driver')
			->orderBy('created_at', 'DESC')
			->paginate(TruckBooking::COUNT_ON_PAGE);
		return TruckBookingResource::collection($orders);
	}

	public function truckCompleted(Request $request)
	{
		$orders = TruckBooking::where(
			['driver_id' => $request->input('authUser')->id, 'status' => TruckBooking::STATUS_DONE]
		)
			->withCarType()
			->withCargoType()
			->withLoadType()
			->with('user', 'driver')
			->orderBy('created_at', 'DESC')
			->paginate(TruckBooking::COUNT_ON_PAGE);
		return TruckBookingResource::collection($orders);
	}

	public function clientOrders(Request $request)
	{
		$status = $request->input('status', null);
		$page = $request->query->getInt('page', 1);
		$countOnPage = TruckBooking::COUNT_ON_PAGE;


		$authUser = $request->authUser;
		$company_id = 0;
		if (in_array($status, TruckBooking::statuses())) {
			$statusArr = [];
			if ($status == TruckBooking::STATUS_ORDER) {
				$statusArr = [TruckBooking::STATUS_ORDER, TruckBooking::STATUS_NEW];
			}
			if ($status == TruckBooking::STATUS_PROCESSING) {
				// $statusArr = ['waiting', 'accepted', 'arrived', 'processing', 'pause', 'confirmation'];
				$statusArr = [
					TruckBooking::STATUS_WAITING,
					TruckBooking::STATUS_ACCEPTED,
					TruckBooking::STATUS_ARRIVED,
					TruckBooking::STATUS_PROCESSING,
					TruckBooking::STATUS_PAUSE,
					TruckBooking::STATUS_CONFIRMATION
				];
			}
			if ($status == TruckBooking::STATUS_DONE) {
				$statusArr = [TruckBooking::STATUS_DONE/*, 'canceled'*/];
			}
			$clientCompany = $this->resolveClientCompanyFromRequest($request);
			$company_id = $clientCompany ? $clientCompany->id : 0;

			$orders = TruckBooking::where('user_id', $authUser->id)
				->whereIn('status', $statusArr)
				->withCarType()
				->where(function ($q) use ($company_id) {
					$q->whereOr('client_company_id', $company_id)
						->whereOr('client_company_id', null);
				})
				->withCargoType()
				->withLoadType()
				->with('user', 'driver', 'bookingPriceOffer', 'bookingPriceOffer.driver', 'bookingPriceOffer.driver.car.carType')
				// ->orderBy('id', 'DESC')->paginate();
				->orderBy('id', 'DESC')
				->paginate($countOnPage);
		} else {
			$orders = TruckBooking::where('user_id', $authUser->id)
				->where(function ($q) use ($company_id) {
					$q->where('client_company_id', $company_id)
						->whereOr('client_company_id', null);
				})
				->withCarType()
				->withCargoType()
				->withLoadType()
				->with('user', 'driver', 'bookingPriceOffer', 'bookingPriceOffer.driver', 'bookingPriceOffer.driver.car.carType')
				// ->orderBy('id', 'DESC')->paginate();
				->orderBy('id', 'DESC')
				->paginate($countOnPage);
				// ->get();
		}

		return TruckBookingResource::collection($orders);
	}

	public function leaveReview(TruckBooking $truckBooking, TruckBookingReviewRequest $request)
	{
		try {
			$savedBooking = $this->dispatchSync(new TruckBookingReviewJob($request, $truckBooking));
			$booking = TruckBooking::withCarType()
				->with('user', 'driver')->find($savedBooking->id);
			return TruckBookingResource::make($booking);
		} catch (\Exception $exception) {
			return response()->json($exception, 500);
		}
	}

	public function leaveReviewDriver(TruckBooking $truckBooking, TruckBookingReviewRequest $request)
	{
		try {
			$savedBooking = $this->dispatchSync(new TruckBookingDriverReviewJob($request, $truckBooking));
			$booking = TruckBooking::withCarType()
				->with('user', 'driver')->find($savedBooking->id);
			return TruckBookingResource::make($booking);
		} catch (\Exception $exception) {
			return response()->json($exception, 500);
		}
	}

	public function messages(TruckBooking $truckBooking, Request $request)
	{
		$truckBooking->messages()
			->where('user_id', '!=', $request->input('authUser')->id)
			->update(['read' => 1]);
		return MessageResource::collection($truckBooking->messages);
	}

	public function sendMessage(TruckBooking $truckBooking, SendMessageRequest $request)
	{
		$message = new Message([
			'user_id' => $request->input('authUser')->id,
			'message' => $request->input('message', null),
			'read' => 0
		]);
		try {
			$savedMessage = $this->dispatchSync(new SendMessageJob($message, $truckBooking));
			$messageUpdated = Message::with('user')->find($savedMessage->id);
			return MessageResource::make($messageUpdated);
		} catch (\Exception $exception) {
			return response()->json($exception, 500);
		}
	}

	public function sendPush(Request $request)
	{
		$userId = $request->input('user_id');
		$message = $request->input('message');
		$type = $request->input('type');
		if (!$userId) {
			return response()->json(['message' => 'user_id is empty or invalid'], 422);
		}
		if (!$message) {
			return response()->json(['message' => 'message is empty or invalid'], 422);
		}
		if (!$type) {
			return response()->json(['message' => 'type is empty or invalid'], 422);
		}
		try {
			$this->dispatchSync(new SendPushJob($request, mb_strtolower($type), false));
			return response()->json(['message' => "Sent"]);
		} catch (\Exception $exception) {
			return response()->json($exception, 500);
		}
	}

	public function view(Request $request, TruckBooking $truckBooking)
	{
		try {
			$driver = $request->input('authUser');
			if ($truckBooking->status == TruckBooking::STATUS_ACCEPTED || !$driver) {
				return response()->json(['message' => 'err 1'], 406);
			}

			$carTypeId = $driver->car->car_type_id;
			$view = TruckBookingView::where('driver_id', $driver->id)->where(
				'truck_booking_id',
				$truckBooking->id
			)->first();

			if (($truckBooking->car_type_id == $carTypeId) && !$view) {
				$this->dispatchSync(new TruckBookingViewJob($driver->id, $truckBooking->id));
				return response()->json(['message' => "ok"], 200);
			} else {
				return response()->json(['message' => 'err'], 409);
			}
		} catch (\Throwable $th) {
			return response()->json(['message' => 'err 2'], 500);
		}
	}

	public function dashboardStatistics(Request $request) {
		$startDate = date('Y-m-d', time() - 30*24*3600) . " 00:00:00";
		$endDate = date('Y-m-d', time()) . " 23:59:59";
		$user = $request->authUser;

		$qb = TruckBooking::whereBetween('created_at', [$startDate, $endDate])
					->where('user_id', $user->id);

		$company = $this->resolveClientCompanyFromRequest($request);

		if ($company instanceof Company) {
			$qb->where('client_company_id', $company->id);
		}

		return response()->json([
			'allOrdersCount' => $qb->where('status', '<>', TruckBooking::STATUS_NEW)->count(),
			'processingOrdersCount' => $qb->whereNotIn('status', [TruckBooking::STATUS_NEW, TruckBooking::STATUS_CANCELED])->count(),
			'doneOrdersCount' => $qb->where('status', '=', TruckBooking::STATUS_DONE)->count(),
		]);
	}

	public function confirmationOrders(Request $request)
	{
		$orders = TruckBooking::where('status', TruckBooking::STATUS_CONFIRMATION)->where(
			'last_status_update',
			'<',
			(time() - 7200)
		)->with('user', 'driver')->withLoadType()->withCarType()->get();

		foreach ($orders as $booking) {
			//change status of each order
			$request = new TruckBookingStatusRequest($request->all());
			$request->merge(['update_from_client' => 1, 'status' => TruckBooking::STATUS_DONE]);
			$this->changeState($booking, $request);

			//send notification to each user
			$data = [
				'booking_id' => $booking->id,
				'booking_info' => [
					'id' => $booking->id,
					'status' => $booking->status
				]
			];
			$request->merge([
				'user_id' => $booking->user_id,
				'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
				'message' => ['message' => 'messages.push_messages.booking_auto_confirmed', 'arg' => $booking->id],
				'notification_type' => 'booking'
			]);
			$this->dispatchSync(new SendPushJob($request, '', false, $data));
		}
	}

	public function changeState(TruckBooking $truckBooking, TruckBookingStatusRequest $request)
	{
		$last_time = $truckBooking->last_status_update;
		$last_status = $truckBooking->status;

		//set notification type for apps
		$request->merge(['notification_type' => 'booking']);


		$truckBooking->status = $request->input('status', $truckBooking->status);
		$truckBooking->way_in_progress = $request->input('way_in_progress', $truckBooking->way_in_progress);

		$commissionRate = 0;
		if ($truckBooking->price > 0) {
			// $commissionRate = $truckBooking->commission * 100 / $truckBooking->price;
		} else {
			$commissionRate = 0;
		}

		if ($last_status == TruckBooking::STATUS_ARRIVED && $truckBooking->status == TruckBooking::STATUS_PROCESSING) {
			$pickup_waiting_time = ceil((time() - $last_time) / 60);
			$truckBooking->pickup_waiting_time = $pickup_waiting_time;

			if ($pickup_waiting_time > $truckBooking->pickup_limit) {
				$truckBooking->pickup_overtime = $pickup_waiting_time - $truckBooking->pickup_limit;
				$truckBooking->pickup_price = $truckBooking->pickup_overtime * $truckBooking->pickup_per_minute;
			}
			/*******For Round Trip********/
			if ($truckBooking->way_in_progress == 2 && $truckBooking->is_round_trip == 1) {
				if ($pickup_waiting_time > $truckBooking->pickup_limit) {
					$truckBooking->round_pickup_overtime = $pickup_waiting_time - $truckBooking->pickup_limit;
					$truckBooking->round_pickup_price = $truckBooking->pickup_overtime * $truckBooking->pickup_per_minute;
					$truckBooking->price += $truckBooking->round_pickup_price;
				}
			}
			/*******For Round Trip End********/
			$truckBooking->price += ceil($truckBooking->pickup_price / 10000) * 10000;
			// $truckBooking->commission = ceil($truckBooking->price * $commissionRate / 100);
		}
		if ($last_status == TruckBooking::STATUS_PROCESSING && $truckBooking->status == TruckBooking::STATUS_PAUSE) {
			$driving_time = ceil((time() - $last_time) / 60);
			$truckBooking->driving_time = $driving_time ?? null;
			$truckBooking->save();
		}
		if ($last_status == TruckBooking::STATUS_PAUSE && ($truckBooking->status == TruckBooking::STATUS_PROCESSING || $truckBooking->status == TruckBooking::STATUS_DONE || $truckBooking->status == TruckBooking::STATUS_CANCELED)) {
			$unloading_waiting_time = ceil((time() - $last_time) / 60);

			$truckBooking->unloading_waiting_time = $unloading_waiting_time;

			if ($unloading_waiting_time > $truckBooking->unloading_limit) {
				$truckBooking->unloading_overtime = $unloading_waiting_time - $truckBooking->unloading_limit;
				$truckBooking->unloading_price = $truckBooking->unloading_overtime * $truckBooking->unloading_per_minute;
			}

			/*******For Round Trip********/
			if ($truckBooking->way_in_progress == 2) {
				if ($unloading_waiting_time > $truckBooking->unloading_limit) {
					$truckBooking->round_unloading_overtime = $unloading_waiting_time - $truckBooking->unloading_limit;
					$truckBooking->round_unloading_price = $truckBooking->unloading_overtime * $truckBooking->unloading_per_minute;
					$truckBooking->price += ceil($truckBooking->round_unloading_price / 10000) * 10000;
				}
			}
			/*******For Round Trip End********/

			$truckBooking->price += ceil($truckBooking->unloading_price / 10000) * 10000;
			$truckBooking->commission = ceil($truckBooking->price * $commissionRate / 100);
		}
		$driver_id = $request->input('driver_id', null);
		$cars = $truckBooking->cars;

		if ($last_status == TruckBooking::STATUS_CANCELED) {
			return response()->json([
				'message' => trans('api.booking_cancelled')
			], 403);
		}

		if ($truckBooking->status == TruckBooking::STATUS_ACCEPTED) {
			$OngoingOrdersCount = TruckBooking::where('status', '!=', TruckBooking::STATUS_DONE)
				->where('status', '!=', TruckBooking::STATUS_CANCELED)
				->where('driver_id', '=', $driver_id)
				->count();

			if ($OngoingOrdersCount > 1) {
				return response()->json(
					['message' => 'Вы не можете принять этот заказ, так как у вас уже есть 2 активных заказа'],
					422
				);
			}

			$IsCarValid = 0;
			$car = Car::withCarType()->where('user_id', $driver_id)->first();

			if ($car->moderated == 0) {
				return response()->json(['message' => 'not_moderated'], 422);
			}
			$carType = CarType::where('id', $car->car_type_id)->first();

			$driverCarWeight = $carType->max_weight;
			$carTypeNext = CarType::where('max_weight', '<=', $driverCarWeight)->orderBy('max_weight', 'DESC')->limit(
				2
			)->get();


			foreach ($carTypeNext as $Val) {
				if ($Val->id == $truckBooking->car_type_id) {
					$IsCarValid = 1;
					break;
				}
			}

			if ($IsCarValid == 0) {
				return response()->json(
					['message' => 'Вы не можете принять этот заказ, так как этот заказ не подходит под ваше авто.'],
					422
				);
			}

			if ($truckBooking->driver_id) {
				return response()->json([
					'message' => trans('api.booking_accepted')
				], 403);
			}

			$truckBooking->cars()->detach();
			$truckBooking->driver_id = $driver_id;

			$driver = User::with('profile', 'profile.company')->find($driver_id);

			if (isset($driver)) {
				if (isset($driver->profile) && isset($driver->profile->company)) {
					$truckBooking->company_id = $driver->profile->company->id;
					if ($driver->profile->company->commission_rate_id) {
						$commissionRate = CommissionRate::find($driver->profile->company->commission_rate_id);
						if ($commissionRate) {
							$truckBooking->commission = ceil($truckBooking->price * $commissionRate->commission / 100);
						}
					}
					$truckBooking->save();
				}
			} else {
				return response()->json(['message' => 'Водитель не найден'], 422);
			}

			if ($truckBooking->created_at) {
				$accepting_time = ceil((time() - $truckBooking->created_at->timestamp) / 60);
				$truckBooking->accepting_time = $accepting_time ?? null;
				$truckBooking->save();
			}
			event(new StartOrderListUpdatingEvent($cars));
		}


//        if ($truckBooking->status == TruckBooking::STATUS_DONE) {
//
//			/*******For Round Trip********/
////			if($FlagForRoundTrip == 0 && $truckBooking->is_round_trip == 1)
////			{
////				$truckBooking->status = TruckBooking::STATUS_ACCEPTED;
////				/* $truckBooking->status = TruckBooking::STATUS_WAITING; */
////			}
//
////			$truckBooking->is_one_way_complete = 1;
//			/*******For Round Trip End********/
//        }


		// if ($truckBooking->status == TruckBooking::STATUS_CONFIRMATION) {
		// 	$truckBooking->status = TruckBooking::STATUS_CONFIRMATION;
		// }

		$truckBooking->waiting_time = null;
		$truckBooking->last_status_update = time();

		$truckBooking->save();
		$truckBooking->refresh();

		if ($truckBooking->status == TruckBooking::STATUS_ORDER) {
			$truckBooking->driver_id = null;
			$truckBooking->company_id = null;
			$truckBooking->cars()->detach();
			$truckBooking->findAndAttachCars();
			event(new StartOrderListUpdatingEvent($cars));
		}
		if ($truckBooking->status == TruckBooking::STATUS_CANCELED) {
			$truckBooking->cars()->detach();
			$truckBooking->cancel_reason_id = $request->input('reason_id');
			$truckBooking->cancel_reason_comment = $request->input('cancel_reason');
			$truckBooking->save();
			event(new StartOrderListUpdatingEvent($cars));
		}
		if ($truckBooking->status == TruckBooking::STATUS_NEW) {
			$truckBooking->driver_id = null;
			$truckBooking->company_id = null;
			$truckBooking->cars()->detach();
			$truckBooking->findAndAttachCars();
			event(new SearchStartEvent($truckBooking));
		}

		$booking = TruckBooking::withCarType()
			->withCargoType()
			->withLoadType()
			->with('user', 'driver')->find($truckBooking->id);
		$phone = null;
		if ($booking->driver) {
			$phone = $booking->driver->user->username;
		}
		/*******For Round Trip********/
//		if($truckBooking->is_one_way_complete == 1 && $truckBooking->status == TruckBooking::STATUS_ACCEPTED){
//			$booking->status = TruckBooking::STATUS_WAITING;
//		}
		/*******For Round Trip End********/
		event(new OrderStatusEvent($booking, $booking->getDriverCar(), $phone, $truckBooking->id));
		/*******For Round Trip********/
//		if($truckBooking->is_one_way_complete == 1 && $truckBooking->status == TruckBooking::STATUS_ACCEPTED)
//		{
//			$booking->status = TruckBooking::STATUS_ACCEPTED;
//		}
		/*******For Round Trip End********/

		// $bookingResource = TruckBookingResource::make(['id' => $booking->id, 'status' => $booking->status]);
		$data = ['booking_id' => $booking->id, 'booking_info' => ['id' => $booking->id, 'status' => $booking->status]];

		$status = trans('admin.booking_statuses.' . $booking->status);

		if (($truckBooking->status == TruckBooking::STATUS_PAUSE || $truckBooking->status == TruckBooking::STATUS_DONE) && $request->has(
				'update_from_client'
			)) {
			if ($booking->company_id) {
				$request->merge(['company_id' => $booking->company_id]);
			}
			$request->merge(['title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id]]);
			$request->merge(['user_id' => $booking->driver_id]);

			if ($truckBooking->status == TruckBooking::STATUS_PAUSE) {
				$request->merge(['message' => trans('messages.push_messages.confirmation_rejected')]);
			} else {
				if ($truckBooking->payment_type == TruckBooking::PAY_COMPANY && $truckBooking->client_company_id) {
					$company = Company::find($truckBooking->client_company_id);

					if (!$company instanceof Company) {
						return response()->json(['message' => 'company_not_found'], 422);
					}

					$transaction = new Transaction();
					$transaction->user_id = $truckBooking->user_id;
					$transaction->type = $request->input('type', 'company_debt');
					$transaction->description = 'Снятие денег со счета клиента за заказ №' . $truckBooking->id;
					$transaction->amount = $truckBooking->price;
					$company->balance -= $truckBooking->price;
					$company->save();
					$transaction->company_id = $company->id;
					$transaction->save();
				} else {
					$transaction = new Transaction();
					$transaction->user_id = $truckBooking->driver_id ?? null;
					$transaction->company_id = $truckBooking->company_id ?? null;
					$transaction->type = $request->input('type', 'debt');
					$transaction->description = 'Комиссия за заказ №' . $truckBooking->id;
					$transaction->amount = $truckBooking->commission;
					$transaction->save();

					if ($truckBooking->company_id) {
						$company = Company::find($truckBooking->company_id);
						if ($company) {
							$company->balance = (int)$company->balance - $transaction->amount;
							$company->save();
						}
					} elseif ($truckBooking->driver_id) {
						$user = User::find($truckBooking->driver_id);
						if ($user && $user->profile) {
							$user->profile->balance = (int)$user->profile->balance - $transaction->amount;
							$user->profile->save();
						}
					}
				}

				$request->merge([
					'message' => trans('messages.push_messages.confirmation_accepted'),
				]);
			}
			$this->dispatchSync(new SendPushJob($request, 'cars', false, $data));
		} else {
			$user = User::with('profile')->find($booking->user_id);
			$request->merge([
				'user_id' => $booking->user_id,
				'notification_type' => 'booking'
			]);
			$request->merge(['title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id]]);
			// $request->merge(['message' => 'messages.push_messages.status_confirmation']);

			if ($truckBooking->status == TruckBooking::STATUS_CONFIRMATION) {
				//Scheduled jobs for unconfirmed bookings

				//Sending to user first warning
				$request->merge(
					[
						'message' => [
							'message' => 'messages.push_messages.status_confirmation',
							'arg' => TruckBooking::CONFIRMATION_LIMIT
						]
					]
				);
				$this->dispatchSync(new SendPushJob($request, '', true, $data));

				//Delay and send the warning to the user after CONFIRMATION_LIMIT_RETRY hours
				$request->merge(
					[
						'message' => [
							'message' => 'messages.push_messages.status_confirmation',
							'arg' => TruckBooking::CONFIRMATION_LIMIT_RETRY
						]
					]
				);
				SendDelayedPushJob::dispatch($request->all(), '', true, $data)->delay(
					now()->addHours(TruckBooking::CONFIRMATION_LIMIT_RETRY)
				);
			} else {
				$request->merge(
					[
						'message' => [
							'message' => 'messages.push_messages.status_changed',
							'arg' => trans(
								'messages.booking_statuses.' . $booking->status,
								[],
								$user->profile->language ?? \App::getLocale()
							)
						]
					]
				);
				$this->dispatchSync(new SendPushJob($request, '', true, $data));
			}
		}
		return TruckBookingResource::make($truckBooking);
	}

	public function driverReminderNotification(Request $request)
	{
		$orders = TruckBooking::where('status', TruckBooking::STATUS_ACCEPTED)
			->withCarType()
			->withCargoType()
			->withLoadType()
			->with('user', 'driver')
			->orderBy('created_at', 'DESC')->get();


		$timeLimit = (int)TruckBooking::DRIVER_REMINDER_TIME;

		foreach ($orders as $booking) {
			$pickup_date = $booking->pickup_date;
			$pickup_time = $booking->pickup_time;
			$pickup_date_time = strtotime("$pickup_date $pickup_time");

			$minutes = 0;

			if (((time() - $pickup_date_time) / 60) > 0) {
				if ($booking->last_notification_reminder) {
					$minutes = abs(time() - $booking->last_notification_reminder) / 60;
				}
			}

			if ($minutes >= $timeLimit) {
				$booking = TruckBooking::withCarType()
					->withCargoType()
					->withLoadType()
					->with('user', 'driver')->find($booking->id);
				$bookingResource = TruckBookingResource::make($booking);
				$data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];

				$request->merge([
					'user_id' => $booking->driver_id,
					'title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id],
					'message' => 'messages.push_messages.have_you_started_loading'
				]);
				$booking->last_notification_reminder = time();
				$booking->save();
				$this->dispatchSync(new SendPushJob($request, 'drivers', true, $data));
			}
		}

		/******************For End Loading*******************/
		$orders = TruckBooking::where('status', TruckBooking::STATUS_ARRIVED)
			->withCarType()
			->withCargoType()
			->withLoadType()
			->with('user', 'driver')
			->orderBy('created_at', 'DESC')->get();


		foreach ($orders as $booking) {
			$last_status_update = $booking->last_status_update;
			$pickupLimitTime = $booking->pickup_limit * 60 + $last_status_update;
			$minutes = 0;

			if (time() > $pickupLimitTime) {
				$minutes = abs($booking->last_notification_reminder - time()) / 60;
			}

			if ($minutes >= $timeLimit) {
				$booking = TruckBooking::withCarType()
					->withCargoType()
					->withLoadType()
					->with('user', 'driver')->find($booking->id);
				$bookingResource = TruckBookingResource::make($booking);
				$data = ['booking_id' => $booking->id, 'booking_info' => $bookingResource];

				$request->merge(['user_id' => $booking->driver_id]);
				$request->merge(['title' => ['message' => 'messages.push_messages.order', 'arg' => $booking->id]]);
				$request->merge(['message' => 'messages.push_messages.have_you_ended_loading']);
				$booking->last_notification_reminder = time();
				$booking->save();
				$booking->refresh();
				$this->dispatchSync(new SendPushJob($request, 'drivers', true, $data));
			}
		}
		return;
	}
}
