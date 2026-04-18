<?php

namespace App\Http\Controllers\TAdmin;

use App\Domain\Companies\Exports\CompaniesExport;
use App\Domain\Companies\Exports\CompanyDebtsExport;
use App\Domain\Companies\Filters\CompanyFilter;
use App\Domain\Companies\Jobs\StoreCompanyJob;
use App\Domain\Companies\Jobs\tgSendNewCorporateRequest;
use App\Domain\Companies\Models\Company;
use App\Domain\Companies\Requests\CompanyRequest;
use App\Domain\Users\Jobs\AdminStoreUserJob;
use App\Domain\Users\Jobs\DeleteUserJob;
use App\Domain\Users\Models\User;
use App\Domain\Users\Requests\AdminStoreUserRequest;
use App\Domain\Users\Requests\AdminUpdateUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CompaniesController extends Controller
{
    public function index(Request $request)
    {
        $filter = new CompanyFilter($request);
        $companies = Company::filter($filter)->with('user')->withCount('users')->paginateFilter();

        return view('admin.companies.index', [
            'companies' => $companies,
            'filters' => $filter->filters(),
        ]);
    }

    public function export(Request $request)
    {
        $filter = new CompanyFilter($request);
        $companies = Company::filter($filter)->with('user')->withCount('users')->get();

        $exportData = collect();
        $exportData->push([
            'ID',
            'Наименование',
            'Кол-во транспорта',
            'Телефон',
            'Дата регистрации',
            'Статус',
        ]);

        foreach ($companies as $company) {
            $companyData = [
                $company->id,
                $company->title,
                $company->users_count ?? 0,
                ($company->user) ? $company->user->username:'',
                $company->created_at->format('d.m.Y'),
                $company->active ? trans('admin.active'): trans('admin.not_active'),
            ];
            $exportData->push($companyData);
        }

        $export = new CompaniesExport($exportData);

        return Excel::download($export, config('app.name').' - companies '.(date('Y-m-d')).'.xlsx');
    }

    public function debts(Request $request)
    {
        $companies = Company::with('user')->where('balance', '<', 0)->withCount('users')->paginate();
        return view('admin.companies.debts', [
            'companies' => $companies
        ]);
    }

    public function debtsExport(Request $request)
    {
        $companies = Company::with('user')->where('balance', '<', 0)
            ->withCount('users')->get();

        $exportData = collect();
        $exportData->push([
            'ID',
            'Наименование',
            'Телефон',
            'Дата регистрации',
            'Задолженность',
            'Статус',
        ]);

        foreach ($companies as $company) {
            $companyData = [
                $company->id,
                $company->title,
                ($company->user) ? $company->user->username:'',
                $company->created_at->format('d.m.Y'),
                $company->balance,
                $company->active ? trans('admin.active'): trans('admin.not_active'),
            ];
            $exportData->push($companyData);
        }

        $export = new CompanyDebtsExport($exportData);

        return Excel::download($export, config('app.name').' - companies-debts '.(date('Y-m-d')).'.xlsx');
    }

    public function showOrders(Company $company)
    {
        return view('admin.companies.show-orders', [
            'company' => $company,
            'orders' => collect([]),
        ]);
    }

    public function show(Company $company)
    {
        $company = Company::with('user')->withCount('users')->findOrFail($company->id);
        return view('admin.companies.show', [
            'company' => $company,
            'user' => $company->user,
            'orders' => collect([]),
        ]);
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(CompanyRequest $request, AdminStoreUserRequest $userRequest)
    {
        try {
			/* if($request->input('password') != $request->input('confirmpassword'))
			{
				return redirect()->back()->with('danger', 'Пароль и подтверждение пароля не совпадают');
			} */
			
            $username = $userRequest->input('username', 0);
            $user = User::where('username', $username)->first();
            if ($user) {
                return redirect()->back()->with('danger', 'Этот пользователь уже зарегистрирован в системе');
            }

            $userRequest->merge(['role' => User::ROLE_MERCHANT]);
            $user = $this->dispatchSync(new AdminStoreUserJob($userRequest));
            $existsCompany = Company::where('user_id', $user->id)->first();
            if($existsCompany) {
                return redirect()->back()->with('danger', 'Этот логин уже используется другой компанией');
            }
            $request->merge(['_user_for_company' => $user]);
            $company = $this->dispatchSync(new StoreCompanyJob($request));
            $this->dispatchSync(new tgSendNewCorporateRequest($user, $company));
            return redirect()->route('admin2.companies.webthankyou');
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.store_failed'));
        }

    }
	
	public function companystore(CompanyRequest $request, AdminStoreUserRequest $userRequest)
    {
        try {
			/* if($request->input('password') != $request->input('confirmpassword'))
			{
				return redirect()->back()->with('danger', 'Пароль и подтверждение пароля не совпадают');
			} */
			
            /* $phoneNumber = $userRequest->input('username', 0);
            $user = User::where('username', $phoneNumber)->first();
            if ($user) {
                return redirect()->back()->with('danger', 'Этот номер уже зарегистрирован в системе');
            } */

            $userRequest->merge(['role' => User::ROLE_MERCHANT]);
            $user = $this->dispatchSync(new AdminStoreUserJob($userRequest));
            $existsCompany = Company::where('user_id', $user->id)->first();
            if($existsCompany) {
                return redirect()->back()->with('danger', 'Этот номер уже используется другой компанией');
            }
            $request->merge(['_user_for_company' => $user]);
            $company = $this->dispatchSync(new StoreCompanyJob($request));
            /* return redirect()->route('admin2.companies.show', $company)->with('inner_success', trans('admin.store_success')); */
			return redirect()->route('admin2.companies.thankyou');
            /* return redirect()->back()->with('success', trans('admin.store_success')); */
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', [
            'company' => $company,
            'user' => $company->user,
        ]);
    }

    public function update(Company $company, CompanyRequest $request, AdminUpdateUserRequest $userRequest)
    {
        try {
            $userRequest->merge(['role' => User::ROLE_MERCHANT]);
            $this->dispatchSync(new AdminStoreUserJob($userRequest));
            $this->dispatchSync(new StoreCompanyJob($request, $company));
            return redirect()->route('admin.companies.show', $company)->with('inner_success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(Company $company)
    {
        try {
            $company->deleteImage('logo');
            $company->deleteImage('licence');
            $company->deleteImage('agreement');
            $company->deleteImage('certificate');
            if($company->user) {
                $this->dispatchSync(new DeleteUserJob($company->user));
            }
            $company->delete();
            return redirect()->route('admin.companies.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.companies.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
	
	public function register($number)
    {
		/* echo $number;exit; */
        return view('admin2.companies.register',compact('number'));
    }
	
	public function thankyou()
    {
		/* echo $number;exit; */
        return view('admin2.companies.thank-you');
    }
	
	public function webthankyou()
    {
		/* echo $number;exit; */
        return view('admin2.companies.web-thank-you');
    }
}
