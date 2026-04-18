<?php

namespace App\Domain\Regions\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

use App\Domain\CarTypes\Models\CarType;
use App\Domain\CarTypes\Models\CarTypeRate;

class ChangeDistancesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $request;
    protected $index;

    public function __construct(Request $request, $index)
    {
        $this->request = $request;
        $this->index = $index;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $region_from_ids = $this->request->input('region_from_ids', []);
        $region_to_ids = $this->request->input('region_to_ids', []);
        $distances = $this->request->input('distances', []);
        $region_from_id = $region_from_ids[$this->index];
        $region_to_id = $region_to_ids[$this->index];
        $d = $distances[$this->index];

        $carTypes = CarType::all();
        $season_id = $this->request->input('season_id', 0);

        foreach($carTypes as $carType){
            $rateQuery = CarTypeRate::where('season_id', $season_id)
                                ->where('car_type_id', $carType->id)
                                ->where(function($query) use($region_from_id, $region_to_id){
                                    return $query->where(function($q) use($region_from_id, $region_to_id) {
                                        return $q->where('region_from_id', $region_from_id)
                                            ->where('region_to_id', $region_to_id);
                                    })
                                    ->orWhere(function($q) use($region_from_id, $region_to_id){
                                        return $q->where('region_from_id', $region_to_id)
                                            ->where('region_to_id', $region_from_id);
                                    });
                                });
            $rates = $rateQuery->get();

            foreach($rates as $rate){
                if($rate){
                    $pricesDistance = json_decode($rate->prices_distance, true);
                    $rateRation = 0;
                    $rateIndex = 0;

                    foreach ($pricesDistance as $i => $pd) {
                        if(isset($pd['distance'])){
                            if($pd['distance'] >= $rate->distance_between){
                                $rateRation = $pd['sum'];
                                $rateIndex = $i;
                                break;
                            }
                        }
                        $rateRation = $pd['sum'];
                        $rateIndex = $i;
                    }

                    $calculatedPrice = ($rate->distance_between * $carType->max_weight * $rateRation) + $rate->min_price;
                    
                    $newRate = ($calculatedPrice - $rate->min_price) / ($d * $carType->max_weight );
                    $newPrice = ($d * $carType->max_weight * $newRate) + $rate->min_price;
                    $pricesDistance[$rateIndex]['sum'] = $newRate;
                    $rate->prices_distance = json_encode($pricesDistance);
                    $rate->save();
                }
            }
            $rateQuery->update(['distance_between' => $d]);
        }
    }
}
