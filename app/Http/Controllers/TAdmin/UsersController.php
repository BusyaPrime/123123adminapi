<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 05.04.2019
 * Time: 17:12
 */

namespace App\Http\Controllers\TAdmin;


use App\Domain\PushNotifications\Jobs\SendPushJob;
use App\Domain\PushNotifications\Models\PushNotification;
use App\Domain\Users\Exports\UsersExport;
use App\Domain\Users\Filters\UserFilter;
use App\Domain\Users\Jobs\AdminStoreUserJob;
use App\Domain\Users\Jobs\DeleteUserJob;
use App\Domain\Users\Models\User;
use App\Domain\Users\Requests\AdminStoreUserRequest;
use App\Domain\Users\Requests\AdminUpdateUserRequest;
use App\Domain\Users\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
/* use Illuminate\Support\Facades\Auth; */

class UsersController extends Controller
{
    public function index(Request $request)
    {
		$company = $request->input('_company');
		$user = auth()->user();
        $filter = new UserFilter($request);
		$users = User::leftJoin("user_profiles","users.id","=","user_profiles.user_id")->with('profile')->select('users.*','user_profiles.*', 'user_profiles.user_id as id' )->where("user_profiles.company_id",$company->id)->filter($filter)->paginateFilter();
        return view('admin2.users.index', [
            'users' => $users,
            'filters' => $filter->filters(),
			'is_external' => $company->user->is_external,
        ]);
    }

    public function export(Request $request)
    {
		$user = auth()->user();
		$company = $request->input('_company');
        $filter = new UserFilter($request);
        $users = User::leftJoin("user_profiles","users.id","=","user_profiles.user_id")->with('profile')->where("user_profiles.company_id",$company->id)->filter($filter)->get();

        $exportData = collect();
        $exportData->push([
            'ID',
            'Ф.И.О',
            'Телефон',
            'Telegram',
            'Дата регистрации',
            'Регион',
            'Эл. почта',
            'Статус',
        ]);

        foreach ($users as $user) {
            $userData = [
                $user->id,
                ($user->profile->surname ?? '').' '.($user->profile->name ?? '').' '.($user->profile->middle_name ?? ''),
                $user->username,
                $user->profile->telegram ?? '',
                $user->created_at->format('d.m.Y'),
                ($user->profile && $user->profile->region) ? ($user->profile->region->title ?? ''): '',
                $user->email ?? '',
                $user->active ? trans('admin.active'): trans('admin.not_active'),
            ];
            $exportData->push($userData);
        }

        $export = new UsersExport($exportData);

        return Excel::download($export, config('app.name').' - users '.(date('Y-m-d')).'.xlsx');
    }

    public function show(User $user,Request $request)
    {
        if ($user->role == User::ROLE_ADMIN) {
            return redirect()->route('admin.users.index');
        }
		$company = $request->input('_company');
        return view('admin2.users.usershow', [
            'user' => $user,
            'orders' => collect([]),
            'notifications' => $user->pushNotifications()->orderBy('created_at', 'desc')->paginate(),
			'is_external' => $company->user->is_external,
        ]);
    }

    public function create(Request $request)
    {
		$user = auth()->user();
		$company = $request->input('_company');
		$user_id = $company->id;
        return view('admin2.users.create',['user_id' => $user_id]);
    }

    public function store(AdminStoreUserRequest $request)
    {
        $request->merge(['role' => null]);

        try {
            $user = $this->dispatchSync(new AdminStoreUserJob($request));
            return redirect()->route('admin2.users.show', $user)->with('success', trans('admin.user_add_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(User $user,Request $request)
    {
        if ($user->role != User::ROLE_CLIENT && $user->role != User::ROLE_DRIVER) {
            return redirect()->route('admin2.users.index');
        }
		$userDt = auth()->user();
		$company = $request->input('_company');
		$user_id = $company->id;
        return view('admin2.users.edit', [
            'user' => $user,
			'user_id' => $user_id,
			'is_external' => $company->user->is_external,
        ]);
    }

    public function update(User $user, AdminUpdateUserRequest $request)
    {
        if ($user->role != User::ROLE_CLIENT && $user->role != User::ROLE_DRIVER) {
            return redirect()->route('admin2.users.index');
        }

        try {
            $request->merge(['role' => null]);
            $user = $this->dispatchSync(new AdminStoreUserJob($request));
            return redirect()->route('admin2.users.show', $user)->with('success', trans('admin.user_update_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(User $user)
    {
        if ($user->role != User::ROLE_CLIENT && $user->role != User::ROLE_DRIVER) {
            return redirect()->route('admin2.users.index');
        }

        try {
            $this->dispatchSync(new DeleteUserJob($user));
            return redirect()->route('admin2.users.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin2.users.index')->with('danger', trans('admin.destroy_failed'));
        }
    }

    public function offer(User $user)
    {

        $pdf = \PDF::loadView('documents.offer', [
            'user' => $user
        ]);

        return $pdf->stream('casva-offer.pdf');
    }

    public function sendPush(Request $request){
        try {
            $this->dispatchSync(new SendPushJob($request));
            if ($request->filled('user_id')) {
                $user = User::find($request->input('user_id'));
                return redirect()->route('admin.users.show', $user)->with('success', 'Пуш-уведомление отправлено');
            } else {
                return redirect()->route('admin.users.index')->with('success', 'Пуш-уведомление отправлено');
            }
        } catch (\Exception $exception) {
            if ($request->filled('user_id')) {
                $user = User::find($request->input('user_id'));
                return redirect()->route('admin.users.show', $user)->with('danger', 'Не удалось отправить уведомление');
            } else {
                return redirect()->route('admin.users.index')->with('danger', 'Не удалось отправить уведомление');
            }
        }
    }

    public function destroyPush(User $user, PushNotification $notification)
    {
        try {
            $notification->delete();
            return redirect()->route('admin.users.show', $user)->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.users.show', $user)->with('danger', trans('admin.destroy_failed'));
        }
    }
}
