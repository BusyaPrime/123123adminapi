<?php

namespace App\Domain\Users\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


use Illuminate\Http\Request;
use App\Domain\Users\Models\User;

class UploadDriverDocJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $request;
     protected $user;

    public function __construct(Request $request, User $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->request->hasFile('licence')) {
            if(isset($this->request->file('licence')['front']) && isset($this->request->file('licence')['back'])){
                $this->user->profile->deleteLicence();
                $this->user->profile->licence = $this->user->profile->uploadLicence($this->request->file('licence'));
            }
        }
        if (isset($this->request->file('car_licence')['front']) && isset($this->request->file('car_licence')['back'])) {
            $this->user->profile->deleteCarLicence();
            $this->user->profile->car_licence = $this->user->profile->uploadCarLicence($this->request->file('car_licence'));
        }
        $this->user->save();
        $this->user->profile->save();
    }
}
