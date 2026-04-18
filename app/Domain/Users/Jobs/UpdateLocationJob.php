<?php


namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\UserProfile;
use App\Domain\Users\Requests\UpdateLocationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateLocationJob
{
    use Dispatchable, Queueable;

    private $request;
    private $profile;

    public function __construct(UpdateLocationRequest $request, UserProfile $profile)
    {
        $this->request = $request;
        $this->profile = $profile;
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
            $profile = $this->profile;

            $profile->lat = $this->request->input('lat', null);
            $profile->lng = $this->request->input('lng', null);
            $profile->save();
            
            $profile->refresh();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $this->profile;
    }
}
