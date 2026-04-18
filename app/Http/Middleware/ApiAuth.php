<?php


namespace App\Http\Middleware;

use App\Domain\AppVersions\Models\AppVersion;
use App\Domain\Users\Models\UserToken;
use Closure;

class ApiAuth
{
    /**
     * @param $request
     * @param Closure $next
     * @param mixed ...$roles
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
		$CurrentVersionNo = $request->header('currentappversionno', '');
		$AppType = $request->header('apptype', '');
		$UserRole = $request->header('userrole', '');

        $CurrentVersionQuery = AppVersion::query()
                        ->where('is_active', 1)
                        ->where('is_deleted', 0)
                        ->where('app_type', $AppType)
                        ->where('userrole', $UserRole)
                        ->where('status_id', 1)
                        ->orderBy('id', 'desc')->get();

		$ArrayToCheck = array();
		$LatestSupportedVersion = null;
		if(isset($CurrentVersionQuery[0])){
			foreach($CurrentVersionQuery as $Val)
			{
				$CurrentVersionNo = $Val->version_no;
				array_push($ArrayToCheck, $CurrentVersionNo);
				if ($LatestSupportedVersion === null || version_compare($CurrentVersionNo, $LatestSupportedVersion, '>')) {
					$LatestSupportedVersion = $CurrentVersionNo;
				}
			}
		}

		$UserCurrentVersion = $request->header('currentappversionno', '');
        if (
            $UserCurrentVersion != "" &&
            !in_array($UserCurrentVersion, $ArrayToCheck) &&
            $LatestSupportedVersion !== null &&
            version_compare($UserCurrentVersion, $LatestSupportedVersion, '<')
		) {
			return response()->json(['message' => 'Please update the application to latest version'], 101);
		}
        
		$token = $request->header('authorization', '');
		$token = str_replace('Bearer ', '', $token);
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $userToken = UserToken::where('token', $token)->first();
        if (!$userToken || !$userToken->user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $user = $userToken->user;

        app()->setLocale($request->header('Accept-Language'));

        if($user->active == 0) {
            return response()->json(["message" => trans('api.user_blocked')], 403);
        }
        $clientCompany = $user->resolveClientCompany(['user']);

        if ($clientCompany && $clientCompany->user && $clientCompany->user->active == 0) {
            return response()->json(["message" => trans('api.user_blocked')], 403);
        }
        if(!$user->hasRole($roles)) {
            return response()->json(["message" => trans('api.user_access_forbidden')], 403);
        }

        $userToken->last_seen_at = time();
        $user->last_seen_at = time();

        $user->save();
        $userToken->save();

        $request->merge(['authToken' => $userToken]);
        $request->merge(['authUser' => $user]);

        if ($clientCompany) {
            $request->merge(['clientCompany' => $clientCompany]);
        }

        // \Log::warning("user name: ".$user->profile->name.' '.$user->profile->surname.", App locale: ".app()->getLocale());
        return $next($request);
    }
}

