<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Companies\Exports\CompaniesExport;
use App\Domain\Companies\Exports\CompanyDebtsExport;
use App\Domain\Companies\Filters\CompanyFilter;
use App\Domain\Companies\Jobs\StoreCompanyJob;
use App\Domain\Companies\Models\Company;
use App\Domain\AppVersions\Models\AppVersion;
use App\Domain\Companies\Requests\CompanyRequest;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Jobs\AdminStoreUserJob;
use App\Domain\Users\Jobs\DeleteUserJob;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserProfile;
use App\Domain\Users\Requests\AdminStoreUserRequest;
use App\Domain\Users\Requests\AdminUpdateUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class AppVersionsController extends Controller
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
        /* $filter = new CompanyFilter($request); */
        /* $appversions = AppVersion::get(); */
		$appversions = AppVersion::where('is_active', '=', 1)->paginate(15);
		/* print_r($appversions);exit; */

        return view('admin.appversions.index', [
            'appversions' => $appversions,
            //'filters' => $filter->filters(),
        ]);
    }
	
	public function filterData(Request $request)
    {
        /* $appversions = AppVersion::get(); */
		$Android = $request->input('Android');
		$iOS = $request->input('iOS');
		$Customer = $request->input('Customer');
		$Driver = $request->input('Driver');
        $appversions = AppVersion::query();
		$appversions2 = $appversions->toSql();
		$appversions1 = "";
		if ($Android == "android" && $iOS == "ios") {
		  $appversions1 .= "WHERE (app_type = '".$Android."' OR app_type = '".$iOS."')";
		}
		if($Android == "android" && $iOS == "")
		{
			$appversions1 .= "WHERE app_type = '".$Android."'";
		}
		if($Android == "" && $iOS == "ios")
		{
			$appversions1 .= "WHERE app_type = '".$iOS."'";
		}
		if($appversions1 == "" && $Customer == "customer" && $Driver == "driver")
		{
			$appversions1 .= "WHERE (userrole = '".$Customer."' OR userrole = '".$Driver."')";
		}
		elseif($appversions1 != "" && $Customer == "customer" && $Driver == "driver")
		{
			$appversions1 .= " AND (userrole = '".$Customer."' OR userrole = '".$Driver."')";
		}
		elseif($appversions1 != "" && $Customer == "customer" && $Driver == "")
		{
			$appversions1 .= " AND (userrole = '".$Customer."')";
		}
		elseif($appversions1 != "" && $Customer == "" && $Driver == "driver")
		{
			$appversions1 .= " AND (userrole = '".$Driver."')";
		}
		elseif($appversions1 == "" && $Customer == "" && $Driver == "driver")
		{
			$appversions1 .= " WHERE (userrole = '".$Driver."')";
		}
		elseif($appversions1 == "" && $Customer == "customer" && $Driver == "")
		{
			$appversions1 .= " WHERE (userrole = '".$Customer."')";
		}
		/* echo "SELECT * FROM app_versions ".$appversions1; */
		$results = DB::SELECT("SELECT * FROM app_versions ".$appversions1);
		
		echo (json_encode($results));exit;
		/* echo $Response;exit; */
    }
	
	public function filterData_Bkp(Request $request)
    {
        /* $appversions = AppVersion::get(); */
		$Android = $request->input('Android');
		$iOS = $request->input('iOS');
		$Customer = $request->input('Customer');
		$Driver = $request->input('Driver');
        $appversions = AppVersion::query();
		if ($Android == "android") {
		  $appversions = $appversions->where('app_type', $Android);
		  if ($iOS == "ios") {
			  $appversions = $appversions->orwhere('app_type', $iOS);
			}
		}
		if ($iOS == "ios") {
		  $appversions = $appversions->where('app_type', $iOS);
		  if ($Android == "android") {
			  $appversions = $appversions->orwhere('app_type', $Android);
			}
		}
		
		if($Customer == "customer")
		{
			$appversions = $appversions->where('userrole', $Customer);
			if ($Driver == "driver") {
			  $appversions = $appversions->orwhere('userrole', $Driver);
			}
		}
		
		if($Driver == "driver")
		{
			$appversions = $appversions->where('userrole', $Driver);
			if ($Customer == "customer") {
			  $appversions = $appversions->orwhere('userrole', $Customer);
			}
		}
		
		$results = $appversions->get();
		echo (json_encode($results));exit;
		/* echo $Response;exit; */
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
        $field = $data['company']->role == Company::ROLE_COMPANY ? 'client_company_id' : 'company_id';
        $bookingsCountNew = TruckBooking::whereIn('status', [TruckBooking::STATUS_ORDER, TruckBooking::STATUS_NEW])
            ->where($field, $data['company']->id)
            ->count();
        $bookingsCountCancelled = TruckBooking::whereIn('status', [TruckBooking::STATUS_CANCELED])
            ->where($field, $data['company']->id)
            ->count();
        $bookingsCountProcessing = TruckBooking::whereIn('status', [
            TruckBooking::STATUS_ACCEPTED,
            TruckBooking::STATUS_WAITING,
            TruckBooking::STATUS_ARRIVED,
            TruckBooking::STATUS_PROCESSING,
            TruckBooking::STATUS_PAUSE,
            TruckBooking::STATUS_CONFIRMATION,
        ])->where($field, $data['company']->id)->count();
        $bookingsCountDone = TruckBooking::where('status', TruckBooking::STATUS_DONE)
            ->where($field, $data['company']->id)
            ->count();

        return view('admin.companies.show', [
            'company' => $data['company'],
            'user' => $data['user'],
            'orders' => collect([]),
            'companyBookingStats' => $this->loadCompanyBookingStats($data['company']),
            'bookingsCountNew' => $bookingsCountNew,
            'bookingsCountProcessing' => $bookingsCountProcessing,
            'bookingsCountDone' => $bookingsCountDone,
            'bookingsCountCancelled' => $bookingsCountCancelled,
        ]);
    }

    public function create(){
        return view('admin.appversions.create');
    }

    public function store(Request $request){
        try {
            
			$data = [];
			$data['version_no'] = $request->input('version_no');
			$data['app_type'] = $request->input('app_type');
			$data['userrole'] = $request->input('userrole');
			$check = DB::table('app_versions')->insertGetId(array(
					'version_no'      => $data['version_no'],
					'app_type'     => $data['app_type'],
					'userrole'     => $data['userrole'],
					'status_id'      => 1,
							));
            return redirect()->route('admin.appversions.index')->with('inner_success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(Company $company)
    {
        return redirect()->route('admin.appversions.index');
    }

    public function update(Request $request)
    {
        try {
            $VersionData = AppVersion::find($request->input('id'));
			/* print_r($VersionData);exit; */
			$VersionData->status_id = 0;
			$VersionData->depricated_date = date('Y-m-d H:i:s');
			/* $VersionData->is_active = 0;
			$VersionData->is_deleted = 1; */
			$VersionData->save();
            return redirect()->route('admin.appversions.index')->with('inner_success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(Request $request)
    {
        try {
			$VersionData = AppVersion::find($request->input('removeid'));
			/* print_r($VersionData);exit; */
			/* $VersionData->status_id = 0; */
			$VersionData->is_active = 0;
			$VersionData->is_deleted = 1;
			$VersionData->save();
            return redirect()->route('admin.appversions.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.appversions.index')->with('danger', trans('admin.destroy_failed'));
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
