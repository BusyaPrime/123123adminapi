<?php

namespace App\Domain\CommissionRate\Jobs;

use App\Domain\CommissionRate\Models\CommissionRate;
use App\Domain\CommissionRate\Requests\CommissionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateCommissionRateJob
{
    use  Dispatchable, Queueable;

    protected $request;
    protected $commissionRate;

    public function __construct(CommissionRequest $request, CommissionRate $commissionRate)
    {
        $this->request = $request;
        $this->commissionRate = $commissionRate;
    }

    public function handle()
    {

        \DB::beginTransaction();
        try {
            $commissionRate = $this->commissionRate;

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
