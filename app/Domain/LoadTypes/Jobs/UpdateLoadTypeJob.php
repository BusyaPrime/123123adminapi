<?php


namespace App\Domain\LoadTypes\Jobs;


use App\Domain\LoadTypes\Models\LoadType;
use App\Domain\LoadTypes\Models\LoadTypeTranslation;
use App\Domain\LoadTypes\Requests\LoadTypeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateLoadTypeJob
{
    use Dispatchable, Queueable;


    public $loadType;

    public $request;

    public function __construct(LoadType $loadType, LoadTypeRequest $request)
    {
        $this->request = $request;
        $this->loadType = $loadType;
    }


    /**
     * Execute the job.
     *
     * @return LoadType
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $loadType = $this->loadType;

            $loadType->priority = $this->request->input('priority', 0);
            $loadType->save();

            $loadType->translations()->delete();
            $translations = [];
            foreach ($this->request->input('translations', []) as $translate) {
                if ($translate['title'] == '') {
                    continue;
                }
                $translations[] = new LoadTypeTranslation($translate);
            }

            if (!empty($translations)) {
                $loadType->translations()->saveMany($translations);
            }
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $loadType;
    }
}
