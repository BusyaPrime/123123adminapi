<?php


namespace App\Domain\TruckBookings\Jobs;

use App\Domain\CarTypes\Models\CarType;
use App\Domain\CarTypes\Models\CarTypeRate;
use App\Domain\Seasons\Models\Season;
use App\Domain\TruckBookings\Models\TruckBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

class CalculateBookingPriceJob
{
    use Queueable, Dispatchable;

    protected $request;
    protected $carType;

    public function __construct(Request $request, CarType $carType)
    {
        $this->request = $request;
        $this->carType = $carType;
    }

    /**
     * @return TruckBooking
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $regionFromId = $this->request->input('region_from_id', null);
            $regionToId = $this->request->input('region_to_id', null);
            $regions = $this->request->input('regions', []);

            $distance = $this->request->input('distance', 0);

            $dimensionX = $this->request->input('dimension_x', null);
            $dimensionY = $this->request->input('dimension_y', null);
            $dimensionZ = $this->request->input('dimension_z', null);

            $isFullRoundTrip = $this->request->input('is_full_round_trip', 0);
            $partialPercentageReq = (int) $this->request->input('partial_percentage', 0);

            $carType = $this->carType;
            $weight = $this->request->input('weight', 0);

            $date = $this->request->input('date', date('Y-m-d'));
            $time = $this->request->input('time', '00:00');
            $time_ratio = 1;

            $season = Season::where('month_start', '<=', date('n', strtotime($date)))
                ->where('month_end', '>=', date('n', strtotime($date)))->first();
            $timeExploded = explode(':', $time);
            $timeSeconds = (($timeExploded[0] ?? 0) * 3600) + (($timeExploded[1] ?? 0) * 60);

            $rate = CarTypeRate::where('car_type_id', $carType->id)
                ->where('region_from_id', $regionFromId)
                ->where('region_to_id', $regionToId)
                ->where('season_id', $season->id ?? 0)->first();

            if ($rate) {
                $timeRatios = json_decode($rate->time_ratio, true);
                $pricesDistance = json_decode($rate->prices_distance, true);

                $lastRateRation = 0;
                $rateRation = 0;

                foreach ($pricesDistance as $distancePrice) {
                    $d = $distancePrice['distance'] ?? 0;
                    if ($distance <= $d) {
                        $rateRation = $distancePrice['sum']??0;
                        break;
                    }
                    $lastRateRation = $distancePrice['sum'];
                }

                if ($rateRation <= 0) {
                    $rateRation = $lastRateRation;
                }

                $fullWeight = $carType->max_weight;
                $carTypeVolume = $carType->dimension_x * $carType->dimension_y * $carType->dimension_z;
                $calculatedPrice = ($distance * $fullWeight * $rateRation) + $rate->min_price;

                if (
                    $distance > $rate->distance_between &&
                    $regionFromId != $regionToId && $isFullRoundTrip == 0 &&
                    $rate->distance_between != 0 &&
                    isset($rate->over_limit_coeff) && count($regions) == 2
                    ) {
                    $calcDistance = $distance - $rate->distance_between;
                    $calculatedPrice = ($rate->distance_between * $fullWeight * $rateRation) + $rate->min_price + ($calcDistance * $rateRation * (double) $rate->over_limit_coeff * $fullWeight);
                }

                /* Start : Apply Discount */
                /* Full Round Trip */
                if ($isFullRoundTrip == 1) {

                    $rateTrip = CarTypeRate::where('car_type_id', $carType->id)
                        ->where('region_from_id', $regionToId)
                        ->where('region_to_id', $regionFromId)
                        ->where('season_id', $season->id ?? 0)->first();

                    if ($rateTrip) {
                        $pricesDistanceTrip = json_decode($rateTrip->prices_distance, true);

                        $lastRateRationTrip = 0;
                        $rateRationTrip = 0;
                        foreach ($pricesDistanceTrip as $distancePrice) {
                            if ($distance <= $distancePrice['distance']) {
                                $rateRationTrip = $distancePrice['sum'];
                                break;
                            }
                            $lastRateRationTrip = $distancePrice['sum'];
                        }
                        if ($rateRationTrip <= 0) {
                            $rateRationTrip = $lastRateRationTrip;
                        }


                        if ($rateTrip->trip_discount) {
                            $calculatedPriceOne = ($distance * $fullWeight * $rateRationTrip) + $rateTrip->min_price;
                            $fullRoundTotal = $calculatedPrice + $calculatedPriceOne;
                            $discount = ($fullRoundTotal * $rate->trip_discount) / 100;
                        } else {
                            $calculatedPriceOne = ($distance * $fullWeight * $rateRation) + $rate->min_price;
                            $fullRoundTotal = $calculatedPrice + $calculatedPriceOne;
                            $discount = ($fullRoundTotal * 0) / 100;
                        }
                        $calculatedPrice = $fullRoundTotal - $discount;
                    }
                }

                $partial_percentages = json_decode($carType->partial_percentages, true);

                if ($partial_percentages && $isFullRoundTrip != 1) {

                    $enteredVolume = $dimensionX * $dimensionY * $dimensionZ;
                    $carLoadPercentage = ceil(($enteredVolume * 100) / $carTypeVolume);

                    $initialPercentage = 0;
                    foreach ($partial_percentages as $partialPercentage) {
                        $percentage = (int) $partialPercentage;

                        if ($carLoadPercentage <= $percentage && ($carType->max_weight * $percentage) / 100 >= $weight) {
                            $initialPercentage = $percentage;
                            break;
                        }
                    }


                    $initialPercentage = $initialPercentage == 0 ? 100 : $initialPercentage;

                    if ($partialPercentageReq > 0)
                        $initialPercentage = $partialPercentageReq;

                    $carTypePartialWeight = ($carType->max_weight * $initialPercentage) / 100;
                    $ratePrice = $initialPercentage < 100 ? $rate->not_full_min_price : $rate->min_price;
                    $carType->notFullEnabled = true;
                    $carType->notFullEnabledPercentage = $initialPercentage;
                    $partialcalculation = ($distance * $carTypePartialWeight * $rateRation) + $ratePrice;

                    if ($distance > $rate->distance_between && $regionFromId != $regionToId && $isFullRoundTrip == 0 && $rate->distance_between != 0 && $rate->over_limit_coeff) {
                        $calcDistance = $distance - $rate->distance_between;
                        $partialcalculation = ($rate->distance_between * $carTypePartialWeight * $rateRation) + $ratePrice + ($calcDistance * $rateRation * (double) $rate->over_limit_coeff * $carTypePartialWeight);
                    }
                    if ($rate->not_full_ratio > 0 && $initialPercentage < 100) {
                        // $calculatedNotFullPrice = $partialcalculation * $rate->not_full_ratio;
                        $calculatedPrice = $partialcalculation * $rate->not_full_ratio;
                    } else {
                        $calculatedPrice = $partialcalculation;
                    }
                }

                foreach ($timeRatios as $ratio) {
                    $startExploded = explode(':', $ratio['start']);
                    $endExploded = explode(':', $ratio['end']);
                    $startSeconds = (($startExploded[0] ?? 0) * 3600) + (($startExploded[1] ?? 0) * 60);
                    $endSeconds = (($endExploded[0] ?? 0) * 3600) + (($endExploded[1] ?? 0) * 60);
                    if ($timeSeconds >= $startSeconds && $timeSeconds <= $endSeconds) {
                        $time_ratio = $ratio['ratio'];
                    }
                }

                //adding limitations for valleys
                $limited_directions = json_decode($carType->limited_directions, true);


                if (!empty($limited_directions)) {
                    $limited_weight = $carType->limited_weight;
                    if (in_array($regionFromId . '_' . $regionToId, $limited_directions)) {
                        $carType->max_weight = $limited_weight;
                    }
                }

                if ($rate->common_coefficient > 0) {
                    $calculatedPrice = $calculatedPrice * $rate->common_coefficient;
                }

                if(count($regions) > 2 && $isFullRoundTrip !== 1) {
                    $countPoints = count($regions) - 2;
                    $calculatedPrice += $countPoints * $rate->group_load_price;
                }

                if ($calculatedPrice > 0) {
                    $carType->calculatedPrice = ceil(($calculatedPrice * $time_ratio) / 1000) * 1000;
                }
            }

        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();
        return $carType;
    }
}