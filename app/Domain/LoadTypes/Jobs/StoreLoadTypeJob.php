<?php


namespace App\Domain\LoadTypes\Jobs;


use App\Domain\LoadTypes\Models\LoadType;
use App\Domain\LoadTypes\Models\LoadTypeTranslation;
use App\Domain\LoadTypes\Requests\LoadTypeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreLoadTypeJob
{
    use Dispatchable, Queueable;


    protected $request;

    public function __construct(LoadTypeRequest $request)
    {
        $this->request = $request;
    }


    /**
     * @return LoadType
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            $loadType = new LoadType();

            $loadType->priority = $this->request->input('priority', 0);
            $loadType->save();

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

            $loadType->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $loadType;
    }
}
