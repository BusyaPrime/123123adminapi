<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

            $subDomain = explode('.', $request->route()->getDomain());

            if(isset($subDomain[0]) && $subDomain[0] == 'admin') {
                return route('admin.auth.loginForm');
            }
            if(isset($subDomain[0]) && $subDomain[0] == 'merchant') {
                return route('admin2.auth.loginForm');
            }
            return route('admin.auth.loginForm');
        }
    }
}
