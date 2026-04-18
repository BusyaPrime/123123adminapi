<?php


namespace App\Domain\CargoTypes\Jobs;


use App\Domain\CargoTypes\Models\CargoType;
use App\Domain\CargoTypes\Models\CargoTypeTranslation;
use App\Domain\CargoTypes\Requests\CargoTypesRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreCargoTypeJob
{
    use Dispatchable, Queueable;

    protected $request;

    public function __construct(CargoTypesRequest $request)
    {
        $this->request = $request;
    }


    /**
     * @return CargoType
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            $cargoType = new CargoType();

            $cargoType->priority = $this->request->input('priority', 0);
            $cargoType->save();

            $translations = [];
            foreach ($this->request->input('translations', []) as $translate) {
                if ($translate['title'] == '') {
                    continue;
                }
                $translations[] = new CargoTypeTranslation($translate);
            }

            if (!empty($translations)) {
                $cargoType->translations()->saveMany($translations);
            }

            $cargoType->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $cargoType;
    }
}
