<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * @param $request
     * @param Closure $next
     * @param mixed ...$roles
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!$request->user() || !$request->user()->hasRole($roles)) {
            Auth::guard()->logout();
            $request->session()->invalidate();
            return redirect('/');
        }

        return $next($request);
    }
}
