<?php

namespace App\Http\Middleware;

use App\Domain\Companies\Models\Company;
use Closure;
use Illuminate\Support\Facades\Auth;

class MerchantCompany
{
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            Auth::guard()->logout();
            $request->session()->invalidate();
            return redirect('/');
        }

        $company = Company::where('user_id', $request->user()->id)->withCount('users')->first();

        if (!$company || $company->active != 1) {
            Auth::guard()->logout();
            $request->session()->invalidate();
            return redirect('/');
        }

        $request->merge(['_company' => $company]);

        return $next($request);
    }
}
