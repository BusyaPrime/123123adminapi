<?php


namespace App\Domain\CarTypes\Jobs;


use App\Domain\CarTypes\Models\CarType;
use App\Domain\CarTypes\Models\CarTypeTranslation;
use App\Domain\CarTypes\Requests\CarTypeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreCarTypeJob
{
    use Dispatchable, Queueable;

    protected $request;

    public function __construct(CarTypeRequest $request)
    {
        $this->request = $request;
    }


    /**
     * @return CarType
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            $carType = new CarType();

            $carType->priority = $this->request->input('priority', 0);
            $carType->min_distance = $this->request->input('min_distance', 0);
            $carType->min_price = $this->request->input('min_price', 0);
            $carType->price_per_km = $this->request->input('price_per_km', 0);
            $carType->price_per_min = $this->request->input('price_per_min', 0);
            $carType->commission = $this->request->input('commission', 0);

            $carType->pickup_limit = $this->request->input('pickup_limit', 0);
            $carType->pickup_per_minute = $this->request->input('pickup_per_minute', 0);
            $carType->unloading_limit = $this->request->input('unloading_limit', 0);
            $carType->load_time_limit = $this->request->input('load_time_limit', 0);
            $carType->unloading_per_minute = $this->request->input('unloading_per_minute', 0);

            $carType->max_weight = $this->request->input('max_weight', 0);
            $carType->dimension_x = $this->request->input('dimension_x', 0);
            $carType->dimension_y = $this->request->input('dimension_y', 0);
            $carType->dimension_z = $this->request->input('dimension_z', 0);
            $carType->is_multi_region = $this->request->input('is_multi_region', 0);
            $carType->save();

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
