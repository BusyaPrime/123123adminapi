<?php


namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserCode;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class NeedVerifyJob
{
    use Queueable, Dispatchable;

    public $user;
    public $code;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->code = mt_rand(1000, 9999);
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
            $user = $this->user;

            if ($user->username == '998900000000' ||
                $user->username == '998901111111' ||
                $user->username == '998901015884' ||
                $user->username == '998902222222' ||
                $user->username == '998902288773' ||
                $user->username == '998333333333' ||
                $user->username == '998944444444' ||
                $user->username == '998977792777'
                ) {
                $this->code = '1000';
            }

            $user->codes()->delete();
            $user->codes()->save(new UserCode([
                'code' => $this->code
            ]));

            $user->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $user->codes()->first();
    }
}

?>