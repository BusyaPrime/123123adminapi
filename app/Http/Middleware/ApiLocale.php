<?php

namespace App\Http\Middleware;

use Closure;

class ApiLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = substr($request->header('Accept-Language') ?? '', 0, 2);

        if(!empty($locale)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
