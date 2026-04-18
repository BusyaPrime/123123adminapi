<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Companies\Exports\CompaniesExport;
use App\Domain\Companies\Exports\CompanyDebtsExport;
use App\Domain\Companies\Filters\CompanyFilter;
use App\Domain\TruckBookings\Filters\TruckBookingsFilter;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Companies\Jobs\StoreCompanyJob;
use App\Domain\Companies\Models\Company;
use App\Domain\Companies\Requests\CompanyRequest;
use App\Domain\Users\Jobs\AdminStoreUserJob;
use App\Domain\Users\Jobs\DeleteUserJob;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserProfile;
use App\Domain\CompanyPriorities\Models\CompanyPriority;
use App\Domain\Users\Requests\AdminStoreUserRequest;
use App\Domain\Users\Requests\AdminUpdateUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CompaniesController extends Controller
{
    private function loadCompanyBookingStats(Company $company): object
    {
        $field = $company->role == Company::ROLE_COMPANY ? 'client_company_id' : 'company_id';

        return TruckBooking::query()
            ->where($field, $company->id)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status = ? THEN price ELSE 0 END), 0) as done_price_sum,
                COALESCE(SUM(CASE WHEN status = ? THEN commission ELSE 0 END), 0) as done_commission_sum
            ", [
                TruckBooking::STATUS_DONE,
                TruckBooking::STATUS_DONE,
            ])
            ->first();
    }

    public function index(Request $request)
    {
        $filter = new CompanyFilter($request);
        $companies = Company::filter($filter)->with('user')->withCount('users')->paginateFilter();

        // foreach($companies as $company){
        //     TruckBooking::whereIn('user_id', $company->users->count() != 0 ? $company->users->pluck('user_id')->push($company->user_id):[null])->update(['client_company_id' => $company->id]);
        // }

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
        $data = $this->prepareCompanyViewData($company);

        return view('admin.companies.show-orders', [
            'company' => $data['company'],
            'user' => $data['user'],
            'orders' => collect([]),
        ]);
    }

    public function show(Company $company)
    {
        $data = $this->prepareCompanyViewData($company);
        $company = $data['company'];
        $request = new CompanyRequest(['company_id' => $company->id]);
        $filter = new TruckBookingsFilter($request);

        $field = $company->role == Company::ROLE_COMPANY ? 'client_company_id':'company_id';

        $bookingsCountNew = TruckBooking::whereIn('status', [TruckBooking::STATUS_ORDER, TruckBooking::STATUS_NEW])->where($field, $company->id)->count();

        $bookingsCountCancelled = TruckBooking::whereIn('status', [TruckBooking::STATUS_CANCELED])->where($field, $company->id)->count();
        $bookingsCountProcessing = TruckBooking::whereIn('status', [
            TruckBooking::STATUS_ACCEPTED,
            TruckBooking::STATUS_WAITING,
            TruckBooking::STATUS_ARRIVED,
            TruckBooking::STATUS_PROCESSING,
            TruckBooking::STATUS_PAUSE,
            TruckBooking::STATUS_CONFIRMATION
        ])
        ->where($field, $company->id)
        ->count();
        $bookingsCountDone = TruckBooking::where('status', TruckBooking::STATUS_DONE)
        ->where($field, $company->id)
        ->count();
        
        return view('admin.companies.show', [
            'company' => $company,
            'user' => $data['user'],
            'companyBookingStats' => $this->loadCompanyBookingStats($company),
            'bookingsCountNew' => $bookingsCountNew,
            'bookingsCountProcessing' => $bookingsCountProcessing,
            'bookingsCountDone' => $bookingsCountDone,
            'bookingsCountCancelled' => $bookingsCountCancelled,
        ]);
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(CompanyRequest $request, AdminStoreUserRequest $userRequest)
    {
        try {
            $phoneNumber = $userRequest->input('username', 0);
            $user = User::where('username', $phoneNumber)->first();
            if ($user) {
                return redirect()->back()->with('danger', 'Этот номер уже зарегистрирован в системе');
            }

            $userRequest->merge(['role' => User::ROLE_MERCHANT]);
            $user = $this->dispatchSync(new AdminStoreUserJob($userRequest));
            $existsCompany = Company::where('user_id', $user->id)->first();
            if($existsCompany) {
                return redirect()->back()->with('danger', 'Этот номер уже используется другой компанией');
            }
            $request->merge(['_user_for_company' => $user]);
            $company = $this->dispatchSync(new StoreCompanyJob($request));
            return redirect()->route('admin.companies.show', $company)->with('inner_success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(Company $company)
    {
        $data = $this->prepareCompanyViewData($company);
        $company_priorities = CompanyPriority::get()->sortBy('quantity');
        return view('admin.companies.edit', [
            'company' => $data['company'],
            'user' => $data['user'],
            'company_priorities' => $company_priorities,
        ]);
    }

    public function update(Company $company, CompanyRequest $request, AdminUpdateUserRequest $userRequest)
    {
        try {
            $userRequest->merge(['role' => User::ROLE_MERCHANT]);
            $this->dispatchSync(new AdminStoreUserJob($userRequest));
            $this->dispatchSync(new StoreCompanyJob($request, $company));

            return redirect()->route('admin.companies.show', $company)->with('success', trans('admin.update_success'));
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


            $companyUsers = $company->users;

            foreach($companyUsers as $user){
                $user->company_id = null;
                $user->save();
            }

            if($company->user) {
                $this->dispatchSync(new DeleteUserJob($company->user));
            }
            $company->delete();
            return redirect()->route('admin.companies.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.companies.index')->with('danger', trans('admin.destroy_failed'));
        }
    }

    private function prepareCompanyViewData(Company $company): array
    {
        $company = Company::with('user.profile', 'commissionRate')->withCount('users')->findOrFail($company->id);
        $user = $company->user;

        if (!$user) {
            $user = new User([
                'active' => User::STATUS_ACTIVE,
                'is_external' => $company->role === Company::ROLE_COMPANY ? 1 : 0,
                'role' => $company->role === Company::ROLE_COMPANY ? User::ROLE_MERCHANT : User::ROLE_DRIVER,
            ]);
            $user->setRelation('profile', new UserProfile());
            $company->setRelation('user', $user);
        } elseif (!$user->profile) {
            $user->setRelation('profile', new UserProfile());
        }

        return [
            'company' => $company,
            'user' => $user,
        ];
    }
}
