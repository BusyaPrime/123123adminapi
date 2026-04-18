<?php

namespace App\Domain\CommissionRate\Jobs;

use App\Domain\CommissionRate\Models\CommissionRate;
use App\Domain\CommissionRate\Requests\CommissionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreCommissionRateJob
{
    use  Dispatchable, Queueable;

    protected $request;

    public function __construct(CommissionRequest $request)
    {
        $this->request = $request;
    }

    public function handle()
    {

        \DB::beginTransaction();
        try {
            $commissionRate = new CommissionRate();

            $commissionRate->title = $this->request->input('title');
            $commissionRate->commission = $this->request->input('commission');

            $commissionRate->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $commissionRate;
    }
}
