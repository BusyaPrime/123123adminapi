<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Companies\Exports\CompaniesExport;
use App\Domain\Companies\Exports\CompanyDebtsExport;
use App\Domain\Companies\Filters\CompanyFilter;
use App\Domain\Companies\Jobs\StoreCompanyJob;
use App\Domain\MerchantHelpRequests\Models\MerchantHelpRequest;
use App\Domain\Companies\Requests\CompanyRequest;
use App\Domain\Users\Jobs\AdminStoreUserJob;
use App\Domain\Users\Jobs\DeleteUserJob;
use App\Domain\Users\Models\User;
use App\Domain\Users\Requests\AdminStoreUserRequest;
use App\Domain\Users\Requests\AdminUpdateUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class MerchantHelpRequestsController extends Controller
{
    public function index(Request $request)
    {
		$merchanthelprequest = MerchantHelpRequest::orderBy('id', 'DESC')->paginate(15);
        return view('admin.merchanthelprequest.index', [
            'merchanthelprequest' => $merchanthelprequest,
        ]);
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

    public function update(MerchantHelpRequest $request, Request $httpRequest)
    {
        try {
            $request->is_active = 0;
            $request->save();
            return redirect()->route('admin.merchanthelprequests.index', ['page' => $httpRequest->get('page') ?? 1])->with('inner_success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(MerchantHelpRequest $request, Request $httpRequest)
    {
        try {
			$request->delete();
            return redirect()->route('admin.merchanthelprequests.index', ['page' => $httpRequest->get('page') ?? 1])->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.merchanthelprequests.index', ['page' => $httpRequest->get('page') ?? 1])->with('danger', trans('admin.destroy_failed'));
        }
    }
}
