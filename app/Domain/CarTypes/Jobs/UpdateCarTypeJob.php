<?php


namespace App\Domain\CarTypes\Jobs;


use App\Domain\CarTypes\Models\CarType;
use App\Domain\CarTypes\Models\CarTypeTranslation;
use App\Domain\CarTypes\Requests\CarTypeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateCarTypeJob
{
    use Dispatchable, Queueable;

    public $carType;

    public $request;

    public function __construct(CarType $carType, CarTypeRequest $request)
    {
        $this->request = $request;
        $this->carType = $carType;
    }


    /**
     * Execute the job.
     *
     * @return CarType
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $carType = $this->carType;

            $dimensions = json_decode($this->request->input('dimensions', null), true);

            $carType->priority = $this->request->input('priority', 0);
            $carType->min_distance = $this->request->input('min_distance', 0);
            $carType->min_price = $this->request->input('min_price', 0);
            $carType->price_per_km = $this->request->input('price_per_km', 0);
            $carType->price_per_min = $this->request->input('price_per_min', 0);
            $carType->load_time_limit = $this->request->input('load_time_limit', 0);
            $carType->commission = $this->request->input('commission', 0);
            $carType->max_weight = $this->request->input('max_weight', 0);

            $carType->dimension_x = $dimensions['dimension_x'];
            $carType->dimension_y = $dimensions['dimension_y'];
            $carType->dimension_z = $dimensions['dimension_z'];

            $carType->pickup_limit = $this->request->input('pickup_limit', 0);
            $carType->pickup_per_minute = $this->request->input('pickup_per_minute', 0);
            $carType->unloading_limit = $this->request->input('unloading_limit', 0);
            $carType->unloading_per_minute = $this->request->input('unloading_per_minute', 0);
            $carType->is_multi_region = $this->request->input('is_multi_region', 0);

            $partial_percentages = $this->request->input('partial_percentages', null);
            

            if(!empty($partial_percentages)){
                asort($partial_percentages);
                $partial_percentages = array_filter($partial_percentages, function($value){return !is_null($value) && $value != 0;});
                $carType->partial_percentages = json_encode($partial_percentages);
            }
            if($partial_percentages == null || count($partial_percentages) == 0) $carType->partial_percentages = null;
            $carType->save();

            $carType->translations()->delete();
            $translations = [];
            foreach ($this->request->input('translations', []) as $translate) {
                if ($translate['title'] == '') {
                    continue;
                }
                $translations[] = new CarTypeTranslation($translate);
            }

            if (!empty($translations)) {
                $carType->translations()->saveMany($translations);
            }

            
            if ($this->request->hasFile('icon')) {
                $carType->deleteImage();
                $carType->icon = $carType->uploadImage($this->request->file('icon'));
            }

            if ($this->request->hasFile('big_icon')) {
                $carType->big_icon = $carType->uploadImage($this->request->file('big_icon'));
            }

            $carType->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $carType;
    }
}
