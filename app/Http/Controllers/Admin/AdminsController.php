<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 05.04.2019
 * Time: 17:12
 */

namespace App\Http\Controllers\Admin;


use App\Domain\Users\Filters\UserFilter;
use App\Domain\Users\Jobs\DeleteUserJob;
use App\Domain\Users\Jobs\StoreUserJob;
use App\Domain\Users\Jobs\UpdateUserJob;
use App\Domain\Users\Models\User;
use App\Domain\Users\Requests\StoreUserRequest;
use App\Domain\Users\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminsController extends Controller
{
    public function index(Request $request)
    {
        $filter = new UserFilter($request);
        $users = User::admins()->filter($filter)->paginateFilter();

        return view('admin.admins.index', [
            'users' => $users,
            'filters' => $filter->filters(),
        ]);
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(StoreUserRequest $request)
    {
        $request->merge(['role' => User::ROLE_ADMIN]);

        try {
            $this->dispatchSync(new StoreUserJob($request));
            return redirect()->route('admin.admins.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.admins.index')->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(User $user)
    {
        if($user->role != User::ROLE_ADMIN) {
            return redirect()->route('admin.admins.index');
        }
        return view('admin.admins.edit', [
            'user' => $user
        ]);
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        if($user->role != User::ROLE_ADMIN) {
            return redirect()->route('admin.admins.index');
        }

        try {
            $this->dispatchSync(new UpdateUserJob($user, $request));
            return redirect()->route('admin.admins.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.admins.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(User $user)
    {
        if($user->role != User::ROLE_ADMIN) {
            return redirect()->route('admin.admins.index');
        }

        try {
            $this->dispatchSync(new DeleteUserJob($user));
            return redirect()->route('admin.admins.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.admins.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
