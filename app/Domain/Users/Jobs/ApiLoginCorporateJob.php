<?php


namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserProfile;
use App\Domain\Users\Requests\ApiLoginCorporateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class ApiLoginCorporateJob
{
    use Queueable, Dispatchable;

    public $request;

    public function __construct(ApiLoginCorporateRequest $request)
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
            $username = $this->request->input('username');
            $user = User::where('username', $username)->first();
            if($user){
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
