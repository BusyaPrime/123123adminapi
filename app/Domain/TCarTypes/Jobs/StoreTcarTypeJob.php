<?php


namespace App\Domain\TCarTypes\Jobs;


use App\Domain\TCarTypes\Models\TcarType;
use App\Domain\TCarTypes\Models\TcarTypeTranslation;
use App\Domain\TCarTypes\Requests\TCarTypeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreTcarTypeJob
{

    use Dispatchable, Queueable;

    protected $request;

    public function __construct(TCarTypeRequest $request)
    {
        $this->request = $request;
    }


    /**
     * @return TCarType
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            $carType = new TCarType();

            $carType->priority = $this->request->input('priority', 0);
            $carType->min_distance = $this->request->input('min_distance', 0);
            $carType->min_price = $this->request->input('min_price', 0);
            $carType->peoples = $this->request->input('peoples', 0);
            $carType->price_per_km = $this->request->input('price_per_km', 0);
            $carType->price_per_min = $this->request->input('price_per_min', 0);
            $carType->commission = $this->request->input('commission', 0);
            $carType->save();

            $translations = [];
            foreach ($this->request->input('translations', []) as $translate) {
                if ($translate['title'] == '') {
                    continue;
                }
                $translations[] = new TCarTypeTranslation($translate);
            }

            if (!empty($translations)) {
                $carType->translations()->saveMany($translations);
            }

            if ($this->request->hasFile('icon')) {
                $carType->icon = $carType->uploadImage($this->request->file('icon'));
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
