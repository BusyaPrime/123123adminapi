<?php

namespace App\Http\Controllers\Admin;

use App\Domain\AdminRoles\Models\AdminRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminRolesController extends Controller
{
    public function index()
    {
        $roles = AdminRole::orderBy('id', 'desc')->paginate();
        return view('admin.admin-roles.index', [
            'roles' => $roles
        ]);
    }

    public function create()
    {
        return view('admin.admin-roles.create');
    }

    public function store(Request $request)
    {
        try {
            $title = $request->input('title');
            $permissions = $request->input('permissions', []);

            $role = new AdminRole();
            $role->title = $title;
            $role->permissions = json_encode($permissions ?? []);
            $role->save();
            return redirect()->route('admin.admins.roles.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.admins.roles.index')->with('danger', trans('admin.store_failed'));
        }
    }

    public function edit(AdminRole $role)
    {
        return view('admin.admin-roles.edit', [
            'role' => $role
        ]);
    }

    public function update(AdminRole $role, Request $request)
    {
        try {
            $title = $request->input('title');
            $permissions = $request->input('permissions', []);

            $role->title = $title;
            $role->permissions = json_encode($permissions ?? []);
            $role->save();
            return redirect()->route('admin.admins.roles.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.admins.roles.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(AdminRole $role)
    {
        try {
            $role->delete();
            return redirect()->route('admin.admins.roles.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.admins.roles.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
