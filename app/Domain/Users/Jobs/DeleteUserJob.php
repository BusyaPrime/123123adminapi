<?php

namespace App\Domain\Users\Jobs;

use App\Domain\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Domain\PushNotifications\Models\PushNotification;

class DeleteUserJob
{
    use Dispatchable, Queueable;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * DeleteUserJob constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            // PushNotification::where('user_id', $this->user->id)->delete();
            $this->user->adminMessages()->delete();
            $this->user->delete();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();
    }
}
