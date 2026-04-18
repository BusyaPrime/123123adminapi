<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActive
{
    /**
     * @param $request Request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user() || $request->user()->active != 1) {
            Auth::guard()->logout();
            $request->session()->invalidate();
            return redirect('/');
        }

        return $next($request);
    }
}
