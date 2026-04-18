<?php

namespace App\Domain\CarTypes\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Http\Request;

use App\Domain\CarTypes\Models\CarTypeRate;
use App\Domain\CarTypes\Models\CarTypeRatesHistory;

class CarTypeRatesHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rate;
    protected $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CarTypeRate $rate, Request $request)
    {
        $this->rate = $rate;
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rate = $this->rate;

        $history = new CarTypeRatesHistory();

        $history->car_type_id = $rate->car_type_id;
        $history->car_type_rate_id = $rate->id;
        $history->region_from_id = $rate->region_from_id;
        $history->region_to_id = $rate->region_to_id;
        $history->season_id = $rate->season_id;


        //Variables from input  to check
        $divider = $this->request->has('divider') ? $this->request->input('divider') : null;
        $min_price = $this->request->has('min_price') ? $this->request->input('min_price') : null;
        $not_full_discount = $this->request->has('not_full_discount') ? $this->request->input('not_full_discount') : null;
        $distance = $this->request->has('distance') ? $this->request->input('distance') : null;
        $ratio = $this->request->has('ratio') ? $this->request->input('ratio') : null;
        $not_full_min_value = $this->request->has('not_full_min_value') ? $this->request->input('not_full_min_value') : null;
        $not_full_min_price = $this->request->has('not_full_min_price') ? $this->request->input('not_full_min_price') : null;
        $not_full_ratio = $this->request->has('not_full_ratio') ? $this->request->input('not_full_ratio') : null;
        $trip_discount = $this->request->has('trip_discount') ? $this->request->input('trip_discount') : null;
        $distance_between = $this->request->has('distance_between') ? $this->request->input('distance_between') : null;
        $over_limit_coeff = $this->request->has('over_limit_coeff') ? $this->request->input('over_limit_coeff') : null;
        $common_coefficient = $this->request->input('common_coefficient', null) ?? $this->request->input('common_coeff', null);
        $prices = $this->request->has('prices') ? $this->request->input('prices', []) : null;
        $pricesDistance = $this->request->has('prices_distance') ? $this->request->input('prices_distance', []) : null;

        $ratePrices = [];
        $ratePricesDistance = [];

        if($prices){
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
            $ratePrices = json_encode($ratePrices);
        }

        if($pricesDistance){
            if (isset($pricesDistance['distance'])) {
                foreach ($pricesDistance['distance'] as $k => $_distance) {
                    if ((int)$_distance > 0 && $pricesDistance['sum'][$k] > 0) {
                        $ratePricesDistance[] = [
                            'distance' => $_distance,
                            'sum' => $pricesDistance['sum'][$k],
                        ];
                    }
                }
            }
            $ratePricesDistance = json_encode($ratePricesDistance);
        }

        
        $changed_fields = [];
        $previous_values = [];

        if($divider && $rate->divider != $divider){
            $previous_values[] = [
                'divider' => $rate->divider
            ];
            $changed_fields[] = [
                'divider' => $divider
            ];
        }
        
        if($min_price && $rate->min_price != $min_price){
            $previous_values[] = [
                'min_price' => $rate->min_price
            ];
            $changed_fields[] = [
                'min_price' => $min_price
            ];
        }
        
        if($not_full_discount && $rate->not_full_discount != $not_full_discount){
            $previous_values[] = [
                'not_full_discount' => $rate->not_full_discount
            ];
            $changed_fields[] = [
                'not_full_discount' => $not_full_discount
            ];
        }
        
        if($distance && $rate->distance != $distance){
            $previous_values[] = [
                'distance' => $rate->distance
            ];
            $changed_fields[] = [
                'distance' => $distance
            ];
        }
        
        if($ratio && $rate->ratio != $ratio){
            $previous_values[] = [
                'ratio' => $rate->ratio
            ];
            $changed_fields[] = [
                'ratio' => $ratio
            ];
        }
        
        if($not_full_min_price && $rate->not_full_min_price != $not_full_min_price){
            $previous_values[] = [
                'not_full_min_price' => $rate->not_full_min_price
            ];
            $changed_fields[] = [
                'not_full_min_price' => $not_full_min_price
            ];
        }
        
        if($not_full_min_value && $rate->not_full_min_value != $not_full_min_value){
            $previous_values[] = [
                'not_full_min_value' => $rate->not_full_min_value
            ];
            $changed_fields[] = [
                'not_full_min_value' => $not_full_min_value
            ];
        }
        
        if($not_full_ratio && $rate->not_full_ratio != $not_full_ratio){
            $previous_values[] = [
                'not_full_ratio' => $rate->not_full_ratio
            ];
            $changed_fields[] = [
                'not_full_ratio' => $not_full_ratio
            ];
        }
        
        if($trip_discount && $rate->trip_discount != $trip_discount){
            $previous_values[] = [
                'trip_discount' => $rate->trip_discount
            ];
            $changed_fields[] = [
                'trip_discount' => $trip_discount
            ];
        }
        
        if($distance_between && $rate->distance_between != $distance_between){
            $previous_values[] = [
                'distance_between' => $rate->distance_between
            ];
            $changed_fields[] = [
                'distance_between' => $distance_between
            ];
        }
        
        if($over_limit_coeff && $rate->over_limit_coeff != $over_limit_coeff){
            $previous_values[] = [
                'over_limit_coeff' => $rate->over_limit_coeff
            ];
            $changed_fields[] = [
                'over_limit_coeff' => $over_limit_coeff
            ];
        }

        if($common_coefficient && $rate->common_coefficient != $common_coefficient){
            $previous_values[] = [
                'common_coefficient' => $rate->common_coefficient
            ];
            $changed_fields[] = [
                'common_coefficient' => $common_coefficient
            ];
        }
        
        if($prices && $rate->prices != $ratePrices){
            $previous_values[] = [
                'prices' => $rate->prices
            ];
            $changed_fields[] = [
                'prices' => $ratePrices
            ];
        }
        
        if($pricesDistance && $rate->prices_distance != $ratePricesDistance){
            $previous_values[] = [
                'prices_distance' => $rate->prices_distance
            ];
            $changed_fields[] = [
                'prices_distance' => $ratePricesDistance
            ];
        }
        if(!empty($changed_fields) && !empty($previous_values)){
            $history->changed_fields = json_encode($changed_fields);
            $history->previous_values = json_encode($previous_values);
            $history->save();
        }
    }
}
