<?php


namespace App\Domain\Tcompanies\Jobs;


use App\Domain\Tcompanies\Models\Tcompany;
use App\Domain\Tcompanies\Requests\TcompanyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreCompanyJob
{
    use Dispatchable, Queueable;

    protected $request;
    protected $company;

    public function __construct(TcompanyRequest $request, Tcompany $company = null)
    {
        $this->request = $request;
        $this->company = $company;
    }

    /**
     * @return Tcompany
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            $company = $this->company ?? new Tcompany();

            $company->title = $this->request->input('title');
            $company->contract_number = $this->request->input('contract_number', null);
            $company->address = $this->request->input('address', null);
            $company->phones = json_encode(array_filter($this->request->input('phones', [])));
            $company->emails = json_encode(array_filter($this->request->input('emails', [])));
            $company->company_name = $this->request->input('company_name', null);
            $company->company_city = $this->request->input('company_city', null);
            $company->company_address = $this->request->input('company_address', null);
            $company->post_index = $this->request->input('post_index', null);
            $company->bank = $this->request->input('bank', null);
            $company->bank_account = $this->request->input('bank_account', null);
            $company->oked = $this->request->input('oked', null);
            $company->mfo = $this->request->input('mfo', null);
            $company->inn = $this->request->input('inn', null);
            $company->okonh = $this->request->input('okonh', null);

            $company->save();

        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $company;
    }
}
