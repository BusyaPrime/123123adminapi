<?php


namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserProfile;
use App\Domain\Users\Requests\ApiLoginRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class ApiLoginJob
{
    use Queueable, Dispatchable;

    public $request;

    public function __construct(ApiLoginRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return User
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $phoneNumber = $this->request->input('phone');
            $user = User::where('username', $phoneNumber)->first();

            if(!$user) {
                $user = new User();
                $user->username = $phoneNumber;
                $user->name = null;
                $user->email = null;
                $user->email_verified_at = null;
                $user->password = null;
                $user->active = 1;
                $user->role = User::ROLE_CLIENT;
                $user->save();
                $user->profile()->save(new UserProfile([
                    'name' => null,
                    'language' => \App::getLocale(),
                    'phone_number' => $phoneNumber,
                    'can_call' => 1,
                ]));
            } else {
                $user->save();
            }
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $user;
    }
}
