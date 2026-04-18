<?php


namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserToken;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

class ApiChangeNumberJob
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
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $loggedUser = $this->request->input('authUser');
            $loggedUser->username = $this->user->username;
            $this->user->delete();
            $loggedUser->save();

        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();
    }
}
