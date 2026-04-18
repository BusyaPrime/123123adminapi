<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 05.04.2019
 * Time: 17:08
 */

namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\User;
use App\Domain\Users\Requests\UpdateUserRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;

class UpdateUserJob
{
    use Dispatchable, Queueable;

    public $user;

    public $request;

    public function __construct(User $user, UpdateUserRequest $request)
    {
        $this->request = $request;
        $this->user = $user;
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

            $user->name = $this->request->input('name');
            $user->username = $this->request->input('username');
            $user->email = $this->request->input('email', null);
            $user->email_verified_at = null;
            $user->admin_role_id = $this->request->input('admin_role_id');
            $user->active = $this->request->input('active', 1);

            if($this->request->filled('password')) {
                $user->password = \Hash::make($this->request->input('password'));
            }

            $user->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $user;
    }
}
