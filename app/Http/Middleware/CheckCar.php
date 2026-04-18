<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCar
{
    /**
     * @param $request Request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->input('authUser');
        if (!$user || !$user->car) {
            return response()->json(["message" => trans('api.user_has_not_car')], 405);
        }

        return $next($request);
    }
}
