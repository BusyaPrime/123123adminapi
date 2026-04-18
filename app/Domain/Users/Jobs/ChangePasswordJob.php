<?php

namespace App\Domain\Users\Jobs;

use App\Domain\Users\Models\User;
use App\Domain\Users\Requests\ChangePasswordRequest;
use App\Domain\Users\Requests\UpdateProfileUserRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class ChangePasswordJob
{
    use Dispatchable, Queueable;

    /**
     * @var ChangePasswordRequest
     */
    private $request;

    public function __construct(ChangePasswordRequest $request)
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
            $user = $this->request->user();
            if (!\Hash::check($this->request->input('current_password'), $user->password)) {
                throw new \Exception();
            }
            $user->password = \Hash::make($this->request->input('new_password'));
            $user->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $user;
    }
}
