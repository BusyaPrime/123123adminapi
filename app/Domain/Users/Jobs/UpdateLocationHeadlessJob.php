<?php


namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\UserProfile;
use App\Domain\Users\Requests\UpdateLocationHeadlessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateLocationHeadlessJob
{
    use Dispatchable, Queueable;

    private $request;
    private $profile;

    public function __construct(UpdateLocationHeadlessRequest $request, UserProfile $profile)
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

            $location = $this->request->input('location', null);
            $lat = $location['coords']['latitude'];
            $lng = $location['coords']['longitude'];

            $profile->lat = $lat;
            $profile->lng = $lng;
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
