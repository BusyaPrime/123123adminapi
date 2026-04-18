<?php


namespace App\Domain\CompanyPriorities\Jobs;


use App\Domain\CompanyPriorities\Models\CompanyPriority;
use App\Domain\CompanyPriorities\Requests\CompanyPriorityRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreCompanyPriorityJob
{
    use Dispatchable, Queueable;

    protected $request;
    protected $company_prior;

    public function __construct(CompanyPriorityRequest $request, CompanyPriority $company_prior = null)
    {
        $this->request = $request;
        $this->company_prior = $company_prior;
    }

    /**
     * @return CompanyPriority
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            if ($this->company_prior) {
                $company_prior = $this->company_prior;
            } else {
                $company_prior = new CompanyPriority();
            }
            $company_prior->name = $this->request->input('name');
            $company_prior->quantity = $this->request->input('quantity');

            $company_prior->save();

        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $company_prior;
    }
}
