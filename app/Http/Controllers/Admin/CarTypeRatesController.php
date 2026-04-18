<?php


namespace App\Http\Controllers\Admin;


use App\Domain\CarTypes\Models\CarType;
use App\Domain\CarTypes\Models\CarTypeRate;
use App\Domain\CarTypes\Jobs\CarTypeRatesHistoryJob;
use App\Domain\Regions\Models\Region;
use App\Domain\Seasons\Models\Season;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarTypeRatesController extends Controller
{
    public function edit(CarType $carType, Request $request)
    {
        $defaultRegion = Region::first();
        $defaultSeason = Season::first();
        if (!$defaultRegion || !$defaultSeason) {
            return redirect()->route('admin.car-types.index')->with('danger', 'Добавьте сезоны');
        }
        $regionFromId = $request->input('region_from_id', $defaultRegion->id);
        $regionToId = $request->input('region_to_id', $defaultRegion->id);
        $seasonId = $request->input('season_id', $defaultSeason->id);

        if(!$regionFromId || !$regionToId || !$seasonId) {
            abort(404);
        }

        $rate = CarTypeRate::where('car_type_id', $carType->id)
            ->where('region_from_id', $regionFromId)->where('region_to_id', $regionToId)
            ->where('season_id', $seasonId)->first();
        return view('admin.car-types.rates', [
            'car_type' => $carType,
            'region_from_id' => $regionFromId,
            'region_to_id' => $regionToId,
            'season_id' => $seasonId,
            'rate' => $rate
        ]);
    }

    public function update(CarType $carType, Request $request)
    {
        $regionFromId = $request->input('region_from_id');
        $regionToId = $request->input('region_to_id');
        $seasonId = $request->input('season_id');
        
        if(!$regionFromId || !$regionToId || !$seasonId) {
            abort(404);
        }

        $rate = CarTypeRate::where('car_type_id', $carType->id)
            ->where('region_from_id', $regionFromId)->where('region_to_id', $regionToId)
            ->where('season_id', $seasonId)->first();
        
        //Save the history...
        $this->dispatchSync(new CarTypeRatesHistoryJob($rate, $request));
        
        if (!$rate) {
            $rate = new CarTypeRate();
            $rate->car_type_id = $carType->id;
            $rate->region_from_id = $regionFromId;
            $rate->region_to_id = $regionToId;
            $rate->season_id = $seasonId;
        }
        $rate->divider = $request->input('divider', 0);
        $rate->min_price = $request->input('min_price', 0);
        $rate->not_full_discount = $request->input('not_full_discount', 0);
        $rate->group_load_price = $request->input('group_load_price', 0);
        $rate->distance = $request->input('distance', 0);
        $rate->ratio = $request->input('ratio', 0);
        $rate->not_full_min_price = $request->input('not_full_min_price', 0);
        $rate->not_full_min_value = $request->input('not_full_min_value', 0);
        $rate->not_full_ratio = $request->input('not_full_ratio', 0);
        $rate->trip_discount = $request->input('trip_discount', 0);
        $rate->distance_between = $request->input('distance_between', 0);
        $rate->over_limit_coeff = $request->input('over_limit_coeff', 0);
        $rate->common_coefficient = $request->input('common_coefficient', 0);

        $prices = $request->input('prices', []);
        $ratePrices = [];
        if(isset($prices['weight'])) {
            foreach ($prices['weight'] as $k => $weight) {
                if ((int)$weight > 0 && $prices['sum'][$k] > 0) {
                    $ratePrices[] = [
                        'weight' => $weight,
                        'sum' => $prices['sum'][$k],
                    ];
                }
            }
        }


        $pricesDistance = $request->input('prices_distance', []);
        $ratePricesDistance = [];
        if (isset($pricesDistance['distance'])) {
            foreach ($pricesDistance['distance'] as $k => $distance) {
                if ((int)$distance > 0 && $pricesDistance['sum'][$k] > 0) {
                    $ratePricesDistance[] = [
                        'distance' => $distance,
                        'sum' => $pricesDistance['sum'][$k],
                    ];
                }
            }
        }
        usort($ratePricesDistance, function($a, $b){
            if ($a == $b) {
                return 0;
            }
            return ($a['distance'] < $b['distance']) ? -1 : 1;
        });

        $ratios = $request->input('time_ratio', []);
        $rateRatios = [];
        foreach ($ratios['start'] as $k => $start) {
            if ($start != '' && $ratios['end'][$k] != ''&& $ratios['ratio'][$k] > 0) {
                $rateRatios[] = [
                    'start' => $start,
                    'end' => $ratios['end'][$k],
                    'ratio' => $ratios['ratio'][$k],
                ];
            }
        }

        $rate->prices = json_encode($ratePrices);
        $rate->prices_distance = json_encode($ratePricesDistance);
        $rate->time_ratio = json_encode($rateRatios);
        $rate->save();


        try {
            return redirect()->route('admin.car-types.rates.edit', [$carType, 'region_from_id' => $regionFromId, 'region_to_id' => $regionToId, 'season_id' => $seasonId])->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.car-types.rates.edit', [$carType, 'region_from_id' => $regionFromId, 'region_to_id' => $regionToId, 'season_id' => $seasonId])->with('danger', trans('admin.update_failed'));
        }
    }
}
