<?php


namespace App\Http\Controllers\Api;


use App\Domain\CargoTypes\Models\CargoType;
use App\Domain\CargoTypes\Resources\CargoTypeResource;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\CarTypes\Resources\CarTypeResource;
use App\Domain\CarTypes\Resources\CarTypeBaseResource;
use App\Domain\Companies\Models\Company;
use App\Domain\Contacts\Models\Contact;
use App\Domain\LoadTypes\Models\LoadType;
use App\Domain\LoadTypes\Resources\LoadTypeResource;
use App\Domain\Regions\Models\Region;
use App\Domain\Regions\Resources\RegionResource;
use App\Domain\Sizes\Models\Size;
use App\Domain\Sizes\Resources\SizeResource;
use App\Domain\TCarTypes\Models\TcarType;
use App\Domain\TCarTypes\Resources\TcarTypeResource;
use App\Domain\Tickets\Jobs\StoreTicketJob;
use App\Domain\Tickets\Requests\StoreTicketRequest;
use App\Domain\Tickets\Resources\TicketResource;
use App\Domain\TicketThemes\Models\TicketTheme;
use App\Domain\TicketThemes\Resources\TicketThemesResource;
use App\Domain\Users\Models\User;
use App\Domain\CancelReasons\Models\CancelReason;
use App\Domain\CancelReasons\Resources\CancelReasonResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\TruckBookings\Jobs\CalculateBookingPriceJob;


//PHP ML
use Phpml\ModelManager;

class HandbookController extends Controller
{
    //backup...
    // public function carTypes(Request $request)
    // {
    //     /* $carTypes = CarType::withTranslation()
    //         ->where('max_weight', '>=', $request->input('weight', 0))
    //         ->where('dimension_x', '>=', $request->input('dimension_x', 0))
    //         ->where('dimension_y', '>=', $request->input('dimension_y', 0))
    //         ->where('dimension_z', '>=', $request->input('dimension_z', 0))
    //         ->orderBy('priority')->get(); */

    //     $regionFromId = $request->input('region_from_id', 1);
    //     $regionToId = $request->input('region_to_id', 1);
    //     $isList = $request->input('list');
    //     $isMultiRegion = $regionFromId != $regionToId ? true : false;

	// 	if($request->input('partial_percentage', 0) > 0)
	// 	{
	// 		$carTypes = CarType::withTranslation()
    //         ->where('max_weight', '>=', $request->input('weight', 0))
    //         ->where('dimension_x', '=', $request->input('dimension_x', 0))
    //         ->where('dimension_y', '=', $request->input('dimension_y', 0))
    //         ->where('dimension_z', '=', $request->input('dimension_z', 0))
    //         ->orderBy('priority')->get();
	// 	}
	// 	else
	// 	{
	// 		$carTypes = CarType::withTranslation()
    //         ->where('max_weight', '>=', $request->input('weight', 0))
    //         ->where('dimension_x', '>=', $request->input('dimension_x', 0))
    //         ->where('dimension_y', '>=', $request->input('dimension_y', 0))
    //         ->where('dimension_z', '>=', $request->input('dimension_z', 0))
    //         ->orderBy('priority');
    //         if(!$isMultiRegion){
    //             $carTypes = $carTypes->get();
    //         } else{
    //             $carTypes = $carTypes->where('is_multi_region', 1)->get();
    //         }
	// 	}

    //     $dimensionX = $request->input('dimension_x', 0);
    //     $dimensionY = $request->input('dimension_y', 0);
    //     $dimensionZ = $request->input('dimension_z', 0);
    //     $isFullRoundTrip = $request->input('is_full_round_trip', 0);
	// 	$partialPercentage = $request->input('partial_percentage', 0);
    //     /* $isPartialRoundTrip = $request->input('is_partial_round_trip', 0); */

    //     $weight = $request->input('weight', 0);

    //     $date = $request->input('date', date('Y-m-d'));
    //     $time = $request->input('time', '00:00');
    //     $distance = (int)$request->input('distance', '0');
    //     $time_ratio = 1;
    //     $season = Season::where('month_start', '<=', date('n', strtotime($date)))
    //         ->where('month_end', '>=', date('n', strtotime($date)))->first();
    //     $timeExploded = explode(':', $time);
    //     $timeSeconds = (($timeExploded[0] ?? 0) * 3600) + (($timeExploded[1] ?? 0) * 60);

    //     $carTypesSorted = collect([]);
    //     if (!$isList) {
    //         foreach ($carTypes as $carType) {
    //             $rate = CarTypeRate::where('car_type_id', $carType->id)
    //                 ->where('region_from_id', $regionFromId)
    //                 ->where('region_to_id', $regionToId)
    //                 ->where('season_id', $season->id ?? 0)->first();

    //             $limited_directions = json_decode($carType->limited_directions, true);

    //             if(!empty($limited_directions)){
    //                 $limited_weight = $carType->limited_weight;
    //                 if(in_array($regionFromId.'_'.$regionToId, $limited_directions)){
    //                     $carType->max_weight = $limited_weight;
    //                 }
    //             }


    //             if ($rate) {
    //                 $timeRatios = json_decode($rate->time_ratio, true);
    //                 $pricesDistance = json_decode($rate->prices_distance, true);

    //                 $lastRateRation = 0;
    //                 $rateRation = 0;
    //                 foreach ($pricesDistance as $distancePrice) {
    //                     if ($distance <= $distancePrice['distance']) {
    //                         $rateRation = $distancePrice['sum'];
    //                         break;
    //                     }
    //                     $lastRateRation = $distancePrice['sum'];
    //                 }
    //                 if($rateRation <= 0) {
    //                     $rateRation = $lastRateRation;
    //                 }

    //                 $volumeFullWeight = 0;
    //                 if ($rate->divider > 0) {
    //                     $volumeFullWeight = (($carType->dimension_x * 100) * ($carType->dimension_y * 100) * ($carType->dimension_z * 100)) / $rate->divider;
    //                 }
    //                 $fullWeight = $volumeFullWeight > $weight ? $volumeFullWeight : $weight;

    //                 $volume = $dimensionX * $dimensionY * $dimensionZ;
    //                 $carTypeVolume = $carType->dimension_x * $carType->dimension_y * $carType->dimension_z;



    //                 // if($distance <= $rate->distance_between){
    //                 // $calculatedPrice = ($distance * $fullWeight * $rateRation) + $rate->min_price;
    //                 $calculatedPrice = ($distance * $fullWeight * $rateRation) + $rate->min_price;
                    
    //                 if($distance > $rate->distance_between && $regionFromId != $regionToId && $isFullRoundTrip == 0 && $rate->distance_between != 0  && $rate->over_limit_coeff) {
    //                     $calcDistance = $distance - $rate->distance_between;
    //                     $calculatedPrice = ($rate->distance_between * $fullWeight * $rateRation) + $rate->min_price + ($calcDistance*$rateRation*(double)$rate->over_limit_coeff*$fullWeight);
    //                 }
                    
                    
    //                 /* Start : Apply Discount */
    //                     /* Full Round Trip */
    //                     if($isFullRoundTrip == 1){

    //                         $rateTrip = CarTypeRate::where('car_type_id', $carType->id)
    //                             ->where('region_from_id', $regionToId)
    //                             ->where('region_to_id', $regionFromId)
    //                             ->where('season_id', $season->id ?? 0)->first();

    //                         if($rateTrip){
    //                             $pricesDistanceTrip = json_decode($rateTrip->prices_distance, true);

    //                             $lastRateRationTrip = 0;
    //                             $rateRationTrip = 0;
    //                             foreach ($pricesDistanceTrip as $distancePrice) {
    //                                 if ($distance <= $distancePrice['distance']) {
    //                                     $rateRationTrip = $distancePrice['sum'];
    //                                     break;
    //                                 }
    //                                 $lastRateRationTrip = $distancePrice['sum'];
    //                             }
    //                             if($rateRationTrip <= 0) {
    //                                 $rateRationTrip = $lastRateRationTrip;
    //                             }


    //                             if($rateTrip->trip_discount){
    //                                 $calculatedPriceOne = ($distance * $fullWeight * $rateRationTrip) + $rateTrip->min_price;
    //                                 $fullRoundTotal = $calculatedPrice + $calculatedPriceOne;
    //                                 $discount = ($fullRoundTotal * $rate->trip_discount) / 100;
    //                                 $calculatedPrice = $fullRoundTotal - $discount;
    //                             }
    //                             else
    //                             {
    //                                 $calculatedPriceOne = ($distance * $fullWeight * $rateRationTrip) + $rateTrip->min_price;
    //                                 $fullRoundTotal = $calculatedPrice + $calculatedPriceOne;
    //                                 $discount = ($fullRoundTotal * 0) / 100;
    //                                 $calculatedPrice = $fullRoundTotal - $discount;
    //                             }
    //                         }
    //                     }

    //                     /* Partial Round Trip */
    //                     /* if($isPartialRoundTrip == 1){
    //                         if($rate->not_full_ratio && $rate->trip_discount){
    //                             $partialPrice = ($distance * $fullWeight * $rateRation) + $rate->min_price;
    //                             $surcharge = ($partialPrice * $rate->not_full_ratio) / 100;
    //                             $partOneTotal = $partialPrice + $surcharge;
    //                             $partialTotal = $partOneTotal + $partOneTotal;
    //                             $tripdiscount = ($partialTotal * $rate->trip_discount) / 100;
    //                             $calculatedPrice = $partialTotal - $tripdiscount;
    //                         }
    //                     } */
    //                 /* End : Apply Discount */
					
	// 				if($partialPercentage > 0)
	// 				{
	// 					/* $fullamount = $calculatedPrice; */
	// 					$partial_weight = ($carType->max_weight*$partialPercentage)/100;
	// 					$calculatedPrice = ($distance * $partial_weight * $rateRation) + $rate->not_full_min_price;
	// 					$fullamount = $calculatedPrice;
	// 					/* $partialcalculation = ($fullamount * $partialPercentage) / 100; */
	// 					$partialcalculation = $fullamount;
	// 					/* $calculatedPrice = $fullamount - $partialcalculation; */
	// 					if ($rate->not_full_ratio > 0) {
    //                         $surchargeamt = $partialcalculation * $rate->not_full_ratio;
	// 						$calculatedPrice = $surchargeamt;
    //                     }
	// 				}

    //                 $calculatedNotFullPrice = $calculatedPrice;

    //                 $volumeWeight = 0;
    //                 if ($rate->divider > 0) {
    //                     $volumeWeight = (($dimensionX * 100) * ($dimensionY * 100) * ($dimensionZ * 100)) / $rate->divider;
    //                 }
    //                 $notFullWeight = $volumeWeight > $weight ? $volumeWeight : $weight;

    //                 $volumePercentage = $carTypeVolume > 0 ? ($volume * 100 / $carTypeVolume) : 100;
    //                 $weightPercentage = $carType->max_weight > 0 ? ($notFullWeight * 100 / $carType->max_weight) : 100;

    //                 if ($volumePercentage <= $rate->not_full_min_value && $weightPercentage <= $rate->not_full_min_value) {
    //                     if ($rate->not_full_ratio > 0) {
    //                         $carType->notFullEnabled = true;
    //                         $calculatedNotFullPrice = (($distance * $notFullWeight * $rateRation) + $rate->not_full_min_price) * $rate->not_full_ratio;
    //                     }
    //                 }

    //                 foreach ($timeRatios as $ratio) {
    //                     $startExploded = explode(':', $ratio['start']);
    //                     $endExploded = explode(':', $ratio['end']);
    //                     $startSeconds = (($startExploded[0] ?? 0) * 3600) + (($startExploded[1] ?? 0) * 60);
    //                     $endSeconds = (($endExploded[0] ?? 0) * 3600) + (($endExploded[1] ?? 0) * 60);
    //                     if ($timeSeconds >= $startSeconds && $timeSeconds <= $endSeconds) {
    //                         $time_ratio = $ratio['ratio'];
    //                     }
    //                 }

    //                 // $calculatedPrice = $calculatedPriceDistance > $calculatedPrice? $calculatedPriceDistance: $calculatedPrice;

    //                 if($rate->common_coefficient > 0){
    //                     $calculatedPrice = $calculatedPrice*$rate->common_coefficient;
    //                     $calculatedNotFullPrice = $calculatedNotFullPrice*$rate->common_coefficient;
    //                 }

    //                 if ($calculatedPrice > 0) {
    //                     $carType->calculatedPrice = round($calculatedPrice * $time_ratio);
    //                     $carType->calculatedPriceNotFull = round($calculatedNotFullPrice * $time_ratio);
    //                     $carTypesSorted->add($carType)->sort();
    //                 }
    //             }
    //             // $carType->setPrice($request->input('distance', 0), $request->input('region_id', null));
    //         }
    //     }

    //     // return CarTypeResource::collection($isList ? $carTypes : $carTypesSorted->sortBy('calculatedPrice'));

    //     return CarTypeResource::collection($isList ? $carTypes : $carTypesSorted->sortBy(function($_car, $key) use ($dimensionX, $dimensionY, $dimensionZ){
    //         if($_car->dimension_x == $dimensionX && $_car->dimension_y == $dimensionY && $_car->dimension_z == $dimensionZ){
    //             return $_car->dimension_x;
    //         }
    //         return $_car->calculatedPrice;
    //     }));
    // }
    
    public function carTypes(Request $request)
    {
        $regionFromId = $request->input('region_from_id', 1);
        $regionToId = $request->input('region_to_id', 1);
        $partialPercentageReq = (int) $request->input('partial_percentage', 0);
        $dimensionX = $request->input('dimension_x', 0);
        $dimensionY = $request->input('dimension_y', 0);
        $dimensionZ = $request->input('dimension_z', 0);
        $weight = (int) $request->input('weight', 0);

		if($partialPercentageReq > 0) {
			$carTypes = CarType::withTranslation()
            ->where('max_weight', '>=', $weight)
            ->where('dimension_x', '=', $dimensionX)
            ->where('dimension_y', '=', $dimensionY)
            ->where('dimension_z', '=', $dimensionZ)
            ->orderBy('max_weight', 'ASC')
            ->get();
		}
		else{
			$carTypes = CarType::withTranslation()
            ->where('max_weight', '>=', $weight)
            ->where('dimension_x', '>=', $dimensionX)
            ->where('dimension_y', '>=', $dimensionY)
            ->where('dimension_z', '>=', $dimensionZ)
            ->orderBy('max_weight', 'ASC')
            ->get();
		}
        

        $carTypesSorted = collect([]);
        foreach ($carTypes as $carType) {
            $carTypesSorted->push($this->dispatchSync(new CalculateBookingPriceJob($request, $carType)));
        }
        return CarTypeResource::collection($carTypesSorted->sortBy(function($_car, $key) use ($dimensionX, $dimensionY, $dimensionZ){
            return ($_car->dimension_x == $dimensionX && $_car->dimension_y == $dimensionY && $_car->dimension_z == $dimensionZ) ? $_car->dimension_x : $_car->calculatedPrice;
        }));
    }

    public function cargoTypes()
    {
        return CargoTypeResource::collection(CargoType::withTranslation()->orderBy('priority')->get());
    }

    public function ticketThemes()
    {
        return TicketThemesResource::collection(TicketTheme::withTranslation()->orderBy('priority')->get());
    }

    public function ticket(StoreTicketRequest $request)
    {
        try {
            $ticket = $this->dispatchSync(new StoreTicketJob($request));
            return TicketResource::make($ticket);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    public function sizes_BKP()
    {
        return SizeResource::collection(Size::withTranslation()->orderBy('priority')->get());
    }
	
	public function sizes()
    {
		$totalLoadSizes = Size::withTranslation()->orderBy('priority')->get();
		foreach($totalLoadSizes as $loadSize)
		{
			$carTypes = CarType::withTranslation()
            ->where('dimension_x', '=', $loadSize->dimension_x)
            ->where('dimension_y', '=', $loadSize->dimension_y)
            ->where('dimension_z', '=', $loadSize->dimension_z)
            ->orderBy('priority')->get();
			foreach ($carTypes as $carType) {
                $loadSize->max_weight = $carType->max_weight;
                // $loadSize->partial_30 = ($carType->max_weight*30)/100;
                // $loadSize->partial_50 = ($carType->max_weight*50)/100;
                // $loadSize->partial_70 = ($carType->max_weight*70)/100;
			}
		}
        return SizeResource::collection($totalLoadSizes);
    }
	public function cancelReasons(Request $request)
    {
		$cancelReasons = CancelReason::withTranslation()->orderByDesc('id')->get();
        return CancelReasonResource::collection($cancelReasons);
    }
	
	public function partialLoadSizes(){
		$partialLoadSizes = Size::withTranslation()->orderBy('priority')->get();
        $totalCarTypes = collect([]);

		foreach($partialLoadSizes as $loadSize)
		{
			$carTypes = CarType::withTranslation()
            ->where('dimension_x', '=', $loadSize->dimension_x)
            ->where('dimension_y', '=', $loadSize->dimension_y)
            ->where('dimension_z', '=', $loadSize->dimension_z)
            ->whereNotNull('partial_percentages')
            ->orderBy('priority')->get();
            if($carTypes->count() == 0 || !$carTypes) continue;

			foreach ($carTypes as $carType) {
                $loadSize->max_weight = $carType->max_weight;
                if($carType->partial_percentages)
                    foreach(json_decode($carType->partial_percentages) as $percentage)
                        $loadSize->{"partial_$percentage"} = ($carType->max_weight*$percentage)/100;
			}
            $totalCarTypes->push($loadSize);
		}
        return SizeResource::collection($totalCarTypes);
    }

    public function loadTypes(){
        return LoadTypeResource::collection(LoadType::withTranslation()->orderBy('priority')->get());
    }

    public function truckTypes() {
        $truckTypes =  CarType::withTranslation()->orderBy('priority')->get();

        return CarTypeBaseResource::collection($truckTypes);
    }

    public function regions(){
        $regions = Region::get();
        return RegionResource::collection($regions);
    }

    public function tCarTypes(Request $request)
    {
        $peoples = $request->input('peoples', 0);
        $carTypes = TcarType::where('peoples', '>=', $peoples)->withTranslation()->orderBy('priority')->get();
        foreach ($carTypes as $carType) {
            $carType->setPrice($request->input('distance', 0));
        }
        return TcarTypeResource::collection($carTypes);
    }

    public function apiLevel()
    {
        return response()->json(['android_version' => '3.5.0', 'ios_version' => '3.0.0'], 200);
    }

    public function contacts()
    {
        $contacts = Contact::get();
        $data = [];
        foreach ($contacts as $contact) {
            $data[$contact->key] = $contact->value;
        }
        return response()->json($data, 200);
    }

    public function paymentDriver(User $user, Request $request)
    {
        if (!$user->profile) {
            return response()->json(['message' => 'user_profile_not_found'], 404);
        }

        $amount = $request->input('amount');

        if (!is_numeric($amount)) {
            return response()->json(['message' => 'amount_required'], 422);
        }

        $user->profile->balance += $amount;
        $user->profile->save();

        return response()->json(['message' => 'ok'], 200);
    }

    public function paymentCompany(Company $company, Request $request)
    {
        $amount = $request->input('amount');

        if (!is_numeric($amount)) {
            return response()->json(['message' => 'amount_required'], 422);
        }

        $company->balance += $amount;
        $company->save();

        return response()->json(['message' => 'ok'], 200);
    }

    public function aiPrice(Request $request)
    {
        $file_path = public_path('ml/model_casva-1.json');
        if (!class_exists(ModelManager::class) || !file_exists($file_path)) {
            return response()->json([
                'available' => false,
                'message' => 'ai_price_unavailable',
            ], 200);
        }

        // Load the model from the JSON file.
        $modelManager = new ModelManager();
        $modelManager->restoreFromFile($file_path);

        // The project does not define a stable public payload contract for this
        // experimental route, so fail explicitly instead of returning a fatal.
        return response()->json([
            'available' => false,
            'message' => 'ai_price_not_configured',
        ], 200);

    }

    public function offer(string $lang)
    {
        $path = storage_path('app/offers');
        $file = $path . '/offer-' . $lang . '.pdf';

        if(!is_dir($path) || !is_file($file)) {
            return response('', 404);
        }

        return response()->file($file);
    }
}
