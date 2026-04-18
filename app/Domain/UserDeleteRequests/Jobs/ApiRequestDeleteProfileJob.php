<?php


namespace App\Domain\UserDeleteRequests\Jobs;


use App\Domain\Users\Models\User;
use App\Domain\UserDeleteRequests\Model\UserDeleteRequest;
use App\Domain\UserDeleteRequests\Requests\ApiUserDeleteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class ApiRequestDeleteProfileJob
{
    use Dispatchable, Queueable;

    private $request;
    private $user;

    public function __construct(ApiUserDeleteRequest $request, User $user)
    {
        $this->request = $request;
        $this->user = $user;
    }


    /**
     * Execute the job.
     *
     * @return UserProfile
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $_Req = new UserDeleteRequest();
            $_Req->username = ($this->user->profile->name != '' || $this->user->profile->surname != '' || $this->user->profile->middle_name != '') ? $this->user->profile->name.' '.$this->user->profile->surname.' '.$this->user->profile->middle_name : 'No name';
            $_Req->phone_number = $this->user->username;
            $_Req->status = UserDeleteRequest::STATUS_PENDING;
            $_Req->user_id = $this->user->id;
            $_Req->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $this->user->profile;
    }
}
