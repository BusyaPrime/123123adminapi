<?php

namespace App\Domain\Sizes\Jobs;

use App\Domain\Sizes\Models\Size;
use App\Domain\Sizes\Models\SizeTranslation;
use App\Domain\Sizes\Requests\SizeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreSizeJob
{

    use Dispatchable, Queueable;

    protected $request;

    public function __construct(SizeRequest $request)
    {
        $this->request = $request;
    }


    /**
     * @return Size
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            $size = new Size();

            $size->priority = $this->request->input('priority', 0);
            $size->dimension_x = $this->request->input('dimension_x', 0);
            $size->dimension_y = $this->request->input('dimension_y', 0);
            $size->dimension_z = $this->request->input('dimension_z', 0);
            $size->save();

            $translations = [];
            foreach ($this->request->input('translations', []) as $translate) {
                if ($translate['title'] == '') {
                    continue;
                }
                $translations[] = new SizeTranslation($translate);
            }

            if (!empty($translations)) {
                $size->translations()->saveMany($translations);
            }

            if ($this->request->hasFile('icon')) {
                $size->icon = $size->uploadImage($this->request->file('icon'));
            }

            $size->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $size;
    }
}
