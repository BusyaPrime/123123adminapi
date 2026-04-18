<?php


namespace App\Http\Middleware;

use App\Domain\AppVersions\Models\AppVersion;
use App\Domain\Users\Models\UserToken;
use App\Domain\Companies\Models\Company;
use Closure;

class ApiBindUser
{
    /**
     * @param $request
     * @param Closure $next
     * @param mixed ...$roles
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $token = $request->header('authorization', '');
        $token = str_replace('Bearer ', '', $token);

        if ($token) {
            $userToken = UserToken::where('token', $token)->first();

            if ($userToken && $userToken->user) {
                $user = $userToken->user;

                $userToken->last_seen_at = time();
                $user->last_seen_at = time();

                $user->save();
                $userToken->save();

                $request->merge(['authToken' => $userToken]);
                $request->merge(['authUser' => $user]);
            }
        }

        return $next($request);
    }
}
