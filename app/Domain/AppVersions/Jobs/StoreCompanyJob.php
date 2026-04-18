<?php


namespace App\Domain\AppVersions\Jobs;


use App\Domain\Companies\Models\Company;
use App\Domain\Companies\Requests\CompanyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreCompanyJob
{
    use Dispatchable, Queueable;

    protected $request;
    protected $company;

    public function __construct(CompanyRequest $request, Company $company = null)
    {
        $this->request = $request;
        $this->company = $company;
    }

    /**
     * @return Company
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            if ($this->company) {
                $company = $this->company;
                $user = $this->company->user;
            } else {
                $company = new Company();
                $user = $this->request->input('_user_for_company');
                $company->user_id = $user->id ?? null;
            }

            $company->title = $this->request->input('title');
            $company->contract_number = $this->request->input('contract_number', null);
            $company->commission_rate_id = $this->request->input('commission_rate_id', null);
            $company->address = $this->request->input('address', null);
            $company->phones = json_encode(array_filter($this->request->input('phones', [])));
            $company->emails = json_encode(array_filter($this->request->input('emails', [])));
            $company->company_name = $company->title;
            $company->company_city = $this->request->input('company_city', null);
            $company->company_address = $this->request->input('company_address', null);
            $company->post_index = $this->request->input('post_index', null);
            $company->bank = $this->request->input('bank', null);
            $company->bank_account = $this->request->input('bank_account', null);
            $company->oked = $this->request->input('oked', null);
            $company->mfo = $this->request->input('mfo', null);
            $company->inn = $this->request->input('inn', null);
            $company->okonh = $this->request->input('okonh', null);

            if ($this->request->hasFile('logo')) {
                $company->deleteImage('logo');
                $company->logo = $company->uploadImage($this->request->file('logo'));
            }

            if ($this->request->hasFile('licence')) {
                $company->deleteImage('licence');
                $company->licence = $company->uploadImage($this->request->file('licence'));
            }

            if ($this->request->hasFile('agreement')) {
                $company->deleteImage('agreement');
                $company->agreement = $company->uploadImage($this->request->file('agreement'));
            }

            if ($this->request->hasFile('certificate')) {
                $company->deleteImage('certificate');
                $company->certificate = $company->uploadImage($this->request->file('certificate'));
            }

            $company->active = $this->request->input('active', 0);
            if($user) {
                $user->active = $this->request->input('active', 0);
                $user->save();
            }

            $company->save();

        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $company;
    }
}
