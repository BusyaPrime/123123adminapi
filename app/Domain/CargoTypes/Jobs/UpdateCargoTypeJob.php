<?php


namespace App\Domain\CargoTypes\Jobs;


use App\Domain\CargoTypes\Models\CargoType;
use App\Domain\CargoTypes\Models\CargoTypeTranslation;
use App\Domain\CargoTypes\Requests\CargoTypesRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateCargoTypeJob
{
    use Dispatchable, Queueable;


    public $cargoType;

    public $request;

    public function __construct(CargoType $cargoType, CargoTypesRequest $request)
    {
        $this->request = $request;
        $this->cargoType = $cargoType;
    }


    /**
     * Execute the job.
     *
     * @return CargoType
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $cargoType = $this->cargoType;

            $cargoType->priority = $this->request->input('priority', 0);
            $cargoType->save();

            $cargoType->translations()->delete();
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
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();
        return $cargoType;
    }
}
