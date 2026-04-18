<?php


namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\UserProfile;
use App\Domain\Users\Requests\ApiChangeProfileRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class ApiChangeProfileJob
{
    use Dispatchable, Queueable;

    private $request;
    private $profile;

    public function __construct(ApiChangeProfileRequest $request, UserProfile $profile)
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

            $profile->name = $this->request->input('name', null);
            $profile->middle_name = $this->request->input('middle_name', null);
            $profile->surname = $this->request->input('surname', null);
            $profile->telegram = $this->request->input('telegram', null);
            $profile->can_call = $this->request->input('can_call', 1);
            $profile->have_terminal = $this->request->input('have_terminal', 0);
            $profile->language = $this->request->input('lang', \App::getLocale());

            $profile->birthday = $this->request->input('birthday');
            $profile->licence_number = $this->request->input('licence_number');
            $profile->car_licence_number = $this->request->input('car_licence_number');

            $profile->licence_number = $this->request->input('licence_number');

            if ($this->request->filled('licence_expired_at'))
            {
                $profile->licence_expired_at = date('Y-m-d', strtotime($this->request->input('licence_expired_at')));
            }

            if ($this->request->hasFile('avatar')) {
                $profile->deleteImage();
                $profile->avatar = $profile->uploadImage($this->request->file('avatar'));
            }
            if ($this->request->hasFile('licence')) {
                $profile->deleteLicence();
                $profile->licence = $profile->uploadLicence($this->request->file('licence'));
            }
            if ($this->request->hasFile('car_licence')) {
                $profile->deleteCarLicence();
                $profile->car_licence = $profile->uploadCarLicence($this->request->file('car_licence'));
            }

            $profile->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $this->profile;
    }
}
