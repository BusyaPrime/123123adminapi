<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 05.04.2019
 * Time: 16:59
 */

namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\User;
use App\Domain\Users\Requests\StoreUserRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;

class StoreUserJob
{
    use Queueable, Dispatchable;

    /**
     * @var StoreUserRequest
     */
    public $request;

    /**
     * StoreUserJob constructor.
     * @param StoreUserRequest $request
     */
    public function __construct(StoreUserRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @return User
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            $user = new User();

            $user->role = $this->request->input('role');
            $user->name = $this->request->input('name', null);
            $user->username = $this->request->input('username');
            $user->email = $this->request->input('email', null);
            $user->admin_role_id = $this->request->input('admin_role_id');
            $user->email_verified_at = null;
            $user->password = \Hash::make($this->request->input('password'));
            $user->active = $this->request->input('active', 1);

            $user->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $user;
    }
}
