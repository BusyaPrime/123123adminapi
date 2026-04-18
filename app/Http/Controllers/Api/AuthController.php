<?php


namespace App\Http\Controllers\Api;

use App\Domain\Users\Events\NeedVerify;
use App\Domain\Users\Jobs\ApiChangeNumberJob;
use App\Domain\Users\Jobs\ApiLoginJob;
use App\Domain\Users\Jobs\ApiLoginCorporateJob;
use App\Domain\Users\Requests\ApiLoginCorporateRequest;
use App\Domain\Users\Jobs\ApiSetTokenJob;
use App\Domain\Users\Jobs\NeedVerifyJob;
use App\Domain\Users\Models\User;
use App\Domain\Users\Jobs\AdminStoreUserJob;
use App\Domain\Companies\Jobs\StoreCompanyJob;
use App\Domain\Companies\Jobs\tgSendNewCorporateRequest;
use App\Domain\Users\Models\UserToken;
use App\Domain\Users\Models\UserProfile;
use App\Domain\Users\Requests\AdminStoreUserRequest;
use App\Domain\Companies\Requests\CompanyRequest;
use App\Domain\Companies\Models\Company;
use App\Domain\Users\Models\MerchantHelpRequest;
use App\Domain\Users\Requests\ApiLoginRequest;
use App\Domain\Users\Resources\UserCodeResource;
use App\Domain\Users\Resources\UserTokenResource;
use App\Http\Controllers\Controller;
use App\Services\SmsService\SmsService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(ApiLoginRequest $request){
        try {
            $user = $this->dispatchSync(new ApiLoginJob($request));
            if($user->active == 0) {
                return response()->json(["message" => trans('api.user_blocked')], 403);
            }
            if($user->profile->company_id) {
                $clientCompany = Company::find($user->profile->company_id)->user;
                if($clientCompany->active == 0){
                    return response()->json(["message" => trans('api.user_blocked')], 403);
                }
            }
            if ($user->smsTimeOut() <= 0) {
                $userCode = $this->dispatchSync(new NeedVerifyJob($user));
                event(new NeedVerify($user, $userCode->code));
            } else {
                $userCode = $user->codes()->first();
            }
            return UserCodeResource::make($userCode);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    public function isUniqueLogin(Request $request) {
        return response()->json(['is_unique' => !(User::where('username', $request->input('username'))->first() instanceof User)]);
    }

    public function timeout(User $user)
    {
        if($user->active == 0) {
            return response()->json(["message" => trans('api.user_blocked')], 403);
        }
        return response()->json(collect(['timeout' => $user->smsTimeOut()]));
    }

    public function resend(User $user)
    {
        if($user->active == 0) {
            return response()->json(["message" => trans('api.user_blocked')], 403);
        }
        $timeout = $user->smsTimeOut();
        if ($timeout <= 0) {
            $userCode = $this->dispatchSync(new NeedVerifyJob($user));
                event(new NeedVerify($user, $userCode->code));
            return UserCodeResource::make($userCode);
        } else {
            return response()->json(["message" => trans('api.sms_time_out', ['seconds' => $timeout])], 403);
        }
    }

    public function loginVerify(User $user, Request $request)
    {
        if($user->active == 0) {
            return response()->json(["message" => trans('api.user_blocked')], 403);
        }

        $userCode = $user->codes()->first();
        $code = $request->input('code', null);
        if (!$code) {
            return response()->json([
                "message" => trans('api.invalid_data'),
                "errors" => ['code' => [trans('api.sms_no_code_error')]],
            ], 422);
        }
        if (!$userCode) {
            return response()->json([
                "message" => trans('api.sms_code_wrong'),
                "errors" => ['code' => [trans('api.sms_code_wrong_error')]],
            ], 422);
        }
        if ($userCode->code == $code) {
            try {
                $token = $this->dispatchSync(new ApiSetTokenJob($user, $request));
                $user->codes()->delete();
                return UserTokenResource::make($token);
            } catch (\Exception $exception) {
                return response()->json(["message" => $exception->getMessage()], 500);
            }
        } else {
            return response()->json([
                "message" => trans('api.invalid_data'),
                "errors" => ['code' => [trans('api.sms_code_wrong_error')]],
            ], 422);
        }
    }
    public function passwordResetRequest(Request $request)
    {
        $phone_number = htmlspecialchars(trim($request->input('phone_number')));

        try {
            // $user = $company->user;
            $user = User::where('is_external', 1)->whereHas('profile', function($query) use ($phone_number){
                return $query->where('phone_number', $phone_number);
            })->first();

            if (!$user || $user->active == 0) {
                return response()->json(['message' => 'login.user_blocked'], 403);
            }
            if ($user->active == 0) {
                return response()->json(['message' => 'login.user_blocked'], 403);
            }
            $smsService = new SmsService();

            if ($user->smsTimeOut() <= 0) {
                $userCode = $this->dispatchSync(new NeedVerifyJob($user));
            } else {
                $userCode = $user->codes()->first();
            }
            $smsService->send($user->profile->phone_number, trans('sms.verify-code', ['app' => config('app.name')]) . ': ' . $userCode->code);
            return UserCodeResource::make($userCode);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    // public function passwordResetVerify(Request $request)
    // {
    //     $phone_number = htmlspecialchars(trim($request->input('phone_number')));
    //     $user = User::where('is_external', 1)->whereHas('profile', function ($query) use ($phone_number) {
    //         return $query->where('phone_number', $phone_number);
    //     })->first();

    //     if (!$user || $user->active == 0) {
    //         return response()->json(["message" => 'login.user_blocked'], 403);
    //     }

    //     $userCode = $user->codes()->first();
    //     $code = $request->input('code', null);
    //     if (!$code) {
    //         return response()->json([
    //             "message" => trans('api.invalid_data'),
    //         ], 401);
    //     }
    //     if (!$userCode) {
    //         return response()->json([
    //             "message" => 'login.sms_code_wrong',
    //         ], 403);
    //     }
    //     if ($userCode->code == $code) {
    //         return response()->json(["message" => 'ok'], 201);
    //     } else {
    //         return response()->json([
    //             "message" => 'login.sms_code_wrong',
    //         ], 403);
    //     }
    // }
    
    public function passwordReset(Request $request)
    {
        $phone_number = htmlspecialchars(trim($request->input('phone_number')));
        $user = User::where('is_external', 1)->whereHas('profile', function ($query) use ($phone_number) {
            return $query->where('phone_number', $phone_number);
        })->first();

        if(!$user || $user->active == 0) {
            return response()->json(["message" => 'login.user_blocked'], 403);
        }

        $userCode = $user->codes()->first();
        $code = $request->input('code', null);

        if (!$code) {
            return response()->json([
                "message" => trans('api.invalid_data'),
            ], 401);
        }
        if (!$userCode) {
            return response()->json([
                "message" => 'login.sms_code_wrong',
            ], 403);
        }
        if ($userCode->code != $code) {
            return response()->json([
                "message" => 'login.sms_code_wrong',
            ], 403);
        }

        if($request->has('password') && !empty($request->input('password'))){
            if(\Hash::check($request->input('password'), $user->password)){
                return response()->json(["message" => 'login.same_password'], 403);
            }
            $user->password = \Hash::make($request->input('password'));
            $user->tokens()->delete();
            $user->codes()->delete();
            $user->save();
            return response()->json(["message" => 'login.password_changed'], 200);
        }
    }
    
    public function loginCorporate(ApiLoginCorporateRequest $request)
    {
        $user = $this->dispatchSync(new ApiLoginCorporateJob($request));
        if(!$user) return response()->json(["message" => trans('api.username_password_incorrect')], 403);
        if($user->active == 0) return response()->json(["message" => trans('api.user_blocked')], 403);
        if($user->is_external == 0 && $user->role == User::ROLE_MERCHANT) return response()->json(["message" => trans('api.merchant_user')], 403);
        
        $company = Company::where('user_id', $user->id)->first();

        $password = $request->input('password');
        if (\Hash::check($password, $user->password)) {
            try {
                $token = $this->dispatchSync(new ApiSetTokenJob($user, $request));
                $token->is_agreed_terms = $company->is_agreed_terms;
                $user->codes()->delete();

                return UserTokenResource::make($token);
            } catch (\Exception $exception) {
                return response()->json(["message" => $exception->getMessage()], 500);
            }
        } else {
            return response()->json([
                "message" => trans('api.invalid_data'),
                "errors" => ['code' => [trans('api.incorrect_data')]],
            ], 422);
        }
    }
    public function loginWithToken(Request $request, User $user)
    {
        if($user->active == 0) return response()->json(["message" => trans('api.user_blocked')], 403);
        if($user->is_external == 0 && $user->role == User::ROLE_MERCHANT) return response()->json(["message" => trans('api.merchant_user')], 403);
        try {
            $token = $this->dispatchSync(new ApiSetTokenJob($user, $request));
            $user->codes()->delete();
            return UserTokenResource::make($token);
        } catch (\Exception $exception) {
            return response()->json(["message" => $exception->getMessage()], 500);
        }
    }

    public function tokens(Request $request)
    {
        $token = $request->input('authToken');
        $user = $request->input('authUser');
        return UserTokenResource::collection($user->tokens()->where('id', '!=', $token->id)->get());
    }

    public function logout(Request $request, UserToken $userToken)
    {
        $token = $request->input('authToken');
        $userToken = isset($userToken->id) ?$userToken : $token;
        try {
            $userToken->delete();
            return response()->json(["message" => 'success'], 200);
        } catch (\Exception $exception) {
            return response()->json(["message" => $exception->getMessage()], 500);
        }
    }

    public function changeNumber(ApiLoginRequest $request)
    {
        try {
            $user = $this->dispatchSync(new ApiLoginJob($request));
            if($user->active == 0) {
                return response()->json(["message" => trans('api.user_blocked')], 403);
            }
            if ($user->smsTimeOut() <= 0) {
                $userCode = $this->dispatchSync(new NeedVerifyJob($user));
                event(new NeedVerify($user, $userCode->code));
            } else {
                $userCode = $user->codes()->first();
            }
            return UserCodeResource::make($userCode);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }

    public function changeNumberVerify(User $user, Request $request)
    {
        if($user->active == 0) {
            return response()->json(["message" => trans('api.user_blocked')], 403);
        }
        $userCode = $user->codes()->first();
        $code = $request->input('code', null);
        if (!$code) {
            return response()->json([
                "message" => trans('api.invalid_data'),
                "errors" => ['code' => [trans('api.sms_no_code_error')]],
            ], 422);
        }
        if (!$userCode) {
            return response()->json([
                "message" => trans('api.sms_code_wrong'),
                "errors" => ['code' => [trans('api.sms_code_wrong_error')]],
            ], 422);
        }
        if ($userCode->code == $code) {
            try {
                $this->dispatchSync(new ApiChangeNumberJob($user, $request));
                return response()->json(["message" => 'success'], 200);
            } catch (\Exception $exception) {
                return response()->json(["message" => $exception->getMessage()], 500);
            }
        } else {
            return response()->json([
                "message" => trans('api.invalid_data'),
                "errors" => ['code' => [trans('api.sms_code_wrong_error')]],
            ], 422);
        }
    }
	
	public function verifyUser(User $user, Request $request)
    {
		$data['is_verified'] = 1;
        $UserData = User::where(['id'=>$user->id])->update($data);
		$UserData = UserToken::select('user_tokens.id','user_tokens.user_id','user_tokens.token','user_tokens.device_info','user_tokens.logged_at','user_tokens.last_seen_at')->orderBy('user_tokens.id', 'DESC')->where('user_tokens.user_id', $user->id)->take(1)->get();
		$IsVerified = User::select('users.is_verified','users.is_external')->where(['id'=>$user->id])->get();
		$UserData[0]->is_verified = $IsVerified[0]->is_verified;
		$UserData[0]->is_external = $IsVerified[0]->is_external;
		$GetUserProfile = UserProfile::where('user_id',$user->id)->get();
		if(isset($GetUserProfile[0]) && !empty($GetUserProfile[0]->company_id))
		{
			$GetCompanyData = Company::where('id',$GetUserProfile[0]->company_id)->get();
			$UserData[0]->company_id = $GetUserProfile[0]->company_id;
			$UserData[0]->company_name = $GetCompanyData[0]->title;
		}
		else
		{
			$UserData[0]->company_id = '';
			$UserData[0]->company_name = '';
		}
        return response()->json(["data" => $UserData[0]]);
    }
	
	public function merchantHelpRequest(User $user, Request $request)
    {
        $UserData = User::where(['id'=>$user->id])->get();
		$Username = $UserData[0]->username;
		$data['username'] = $Username;
		$GetCount = MerchantHelpRequest::where(['username'=>$Username])->count();
		if($GetCount < 1)
		{
			$SubmitData = MerchantHelpRequest::create($data);
			return response()->json(["message" => 'success'], 200);
		}
		else
		{
			return response()->json(["message" => 'We have already received your request. Our Customer support Team will reach out to you soon!'], 403);
		}
    }

    public function signUpCorporate(CompanyRequest $request, AdminStoreUserRequest $storeUser){
        try {
            $storeUser->merge([
                'role' => User::ROLE_MERCHANT,
                'active' => 1,
                'is_external' => 1,
            ]);
            $user = $this->dispatchSync(new AdminStoreUserJob($storeUser));
            $existsCompany = Company::where('user_id', $user->id)->first();

            if($existsCompany instanceof Company) {
                return response()->json(["message" => 'exists'], 400);
            }
            
            $request->merge([
                '_user_for_company' => $user,
                'companyRole' => Company::ROLE_COMPANY,
            ]);
            $company = $this->dispatchSync(new StoreCompanyJob($request));

            $storeUser->merge(['company_id' => $company->id, 'role' => User::ROLE_CLIENT, 'username' => $request->phonenumber, 'active' => 1]);
            $storeUser->request->remove('is_external');

            $this->dispatchSync(new AdminStoreUserJob($storeUser));

            $this->dispatchSync(new tgSendNewCorporateRequest($user, $company));
            return response()->json(["message" => 'success'], 200);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(["message" => 'error'], 400);
        }

    }
    
    public function signUpPersonal(AdminStoreUserRequest $storeUser){
        try {
            $user = $this->dispatchSync(new AdminStoreUserJob($storeUser));
            // $request->merge(['_user_for_company' => $user]);
            // $company = $this->dispatchSync(new StoreCompanyJob($request));
            // $this->dispatchSync(new tgSendNewCorporateRequest($user, $company));
            return response()->json(["message" => 'success'], 200);
        } catch (\Exception $exception) {
            return response()->json(["message" => 'error'], 400);
        }

    }
}
