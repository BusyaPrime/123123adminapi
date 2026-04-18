<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class CheckRolePermission
{
    /**
     * @param $request
     * @param Closure $next
     * @param $permission
     * @return RedirectResponse|Redirector|mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = $request->user();
        if (!$user) {
            Auth::guard()->logout();
            $request->session()->invalidate();
            return redirect('/');
        }
        $userPermissions = [];
        $adminRole = $user->adminRole;
        if ($adminRole) {
            $userPermissions = json_decode($adminRole->permissions, true);
        }

        if (!in_array($permission, $userPermissions)) {
            abort(403);
        }

        return $next($request);
    }
}
