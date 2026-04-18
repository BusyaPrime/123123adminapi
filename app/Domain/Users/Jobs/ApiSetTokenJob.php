<?php


namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserToken;
use App\Domain\Users\Models\UserProfile;
use App\Domain\Companies\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

class ApiSetTokenJob
{
    use Dispatchable, Queueable;

    private $request;

    private $user;

    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }


    /**
     * @return false|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $fcm_token = $this->request->input('fcm_token', null);
            $existingFcms = UserToken::where('fcm_token', $fcm_token);

            if($fcm_token && $existingFcms->count() > 0){
                $existingFcms->delete();
            }

            $token = $this->user->tokens()->save(new UserToken([
                'token' => \Hash::make($this->user->username.':'.time().':'.uniqid(time())),
                'device_info' => $this->request->userAgent(),
                'fcm_token' => $fcm_token,
                'logged_at' => time(),
                'last_seen_at' => time(),
            ]));

			/*******05-09-2022******/
			$token->is_verified = $this->user->is_verified;
			$token->is_external = $this->user->is_external;
			if(!empty($this->user->profile->company_id))
			{
				$GetCompanyData = Company::find($this->user->profile->company_id);
				$token->company_id = $this->user->profile->company_id;
				$token->company_name = $GetCompanyData->title;
			}
			else
			{
				$token->company_id = '';
				$token->company_name = '';
			}
			/*************/
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $token;
    }
}
