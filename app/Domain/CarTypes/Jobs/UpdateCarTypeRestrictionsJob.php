<?php


namespace App\Domain\CarTypes\Jobs;


use App\Domain\CarTypes\Models\CarType;
use App\Domain\CarTypes\Models\CarTypeTranslation;
use App\Domain\CarTypes\Requests\CarTypeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Http\Request;

class UpdateCarTypeRestrictionsJob
{
    use Dispatchable, Queueable;

    public $carType;
    public $request;

    public function __construct(CarType $carType, Request $request)
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
            $directions = $this->request->input('directions', []);
            $weight = $this->request->input('restricted_weight', 0);
            
            if(!empty($directions)){
                $directions = $directions['direction'];
                $carType->limited_directions = json_encode($directions);
            }
            else $carType->limited_directions = json_encode([]);
            $carType->limited_weight = $weight;

            $carType->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $carType;
    }
}
