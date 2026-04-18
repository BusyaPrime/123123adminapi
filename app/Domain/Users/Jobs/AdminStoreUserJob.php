<?php


namespace App\Domain\Users\Jobs;


use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class AdminStoreUserJob
{
    use Queueable, Dispatchable;
    public $request;

    public function __construct($request)
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
            $phoneNumber = $this->request->input('username', 0);
            $phoneNumber = str_replace([' ', '(', ')', '-', '+'], '', $phoneNumber);
            $role = $this->request->input('role');
            
            $user = User::where('username', $phoneNumber)->first();
			$cellnumber = "";
			if ($this->request->filled('phonenumber'))
            {
                $cellnumber = $this->request->input('phonenumber');
                $cellnumber = str_replace([' ', '(', ')', '-', '+'], '', $cellnumber);
            }
			$company_id = null;
			if ($this->request->filled('company_id'))
            {
                $company_id = $this->request->input('company_id');
            }
            
			if(!$user) {
                $user = new User();
                $user->username = $phoneNumber;
                $user->name = $this->request->input('name');
                $user->email = $this->request->input('email');
                $user->email_verified_at = null;
                $user->password = null;
                $user->active = $this->request->input('active', 1);
                $user->role = $role ?? User::ROLE_CLIENT;
				if($this->request->input('active') == 1 && !$this->request->filled('company_id'))
				{
					$user->is_verified = 1;
				}
				if((int) $this->request->input('is_external') === 1)
				{
					$user->is_external = 1;
				}
                $user->save();
                $user->profile()->save(new UserProfile([
                    'name' => $this->request->input('name'),
                    'surname' => $this->request->input('surname'),
                    'middle_name' => $this->request->input('middle_name'),
                    'region_id' => $this->request->input('region_id'),
                    'telegram' => $this->request->input('telegram'),
                    'balance' => $this->request->input('balance', 0),
                    'language' => $this->request->input('lang', \App::getLocale()),
                    'can_call' => $this->request->input('can_call', 1),
                    'have_terminal' => $this->request->input('have_terminal', 0),

                    'birthday' => $this->request->input('birthday'),
                    'licence_number' => $this->request->input('licence_number'),
                    'car_licence_number' => $this->request->input('car_licence_number'),
                    'phone_number' => $cellnumber,
                    'company_id' => $company_id,
                ]));
            } else {
                // $user->username = $this->request->input('username', $user->username);
                
                $user->email = $this->request->input('email');
                $user->active = $this->request->input('active', 1);
				if($this->request->input('active') == 1)
				{
					$user->is_verified = 1;
				}
                $user->save();
                $user->profile->name = $this->request->input('name');
                $user->profile->surname = $this->request->input('surname');
                $user->profile->middle_name = $this->request->input('middle_name');
                $user->profile->region_id = $this->request->input('region_id');
                $user->profile->telegram = $this->request->input('telegram');
                //                $user->profile->balance = $this->request->input('balance', 0);
                $user->profile->language = $this->request->input('lang', \App::getLocale());
                $user->profile->can_call = $this->request->input('can_call', 1);
                $user->profile->have_terminal = $this->request->input('have_terminal', 0);
                
                $user->profile->birthday = $this->request->input('birthday');
                $user->profile->licence_number = $this->request->input('licence_number');
                $user->profile->car_licence_number = $this->request->input('car_licence_number');
                $user->profile->phone_number = $cellnumber;
                $user->profile->company_id = $company_id;

				if((int) $this->request->input('is_external') === 1)
				{
					$user->is_external = 1;
				}
                
                $user->profile->save();
                $user->role = $role ?? $user->role;
                $user->save();
            }
            
            if ($this->request->filled('licence_expired_at'))
            {
                $user->profile->licence_expired_at = date('Y-m-d', strtotime($this->request->input('licence_expired_at')));
            }
            
            if ($this->request->hasFile('licence')) {
                $user->profile->deleteLicence();
                $user->profile->licence = $user->profile->uploadLicence($this->request->file('licence'));
            }
            
            if ($this->request->hasFile('car_licence')) {
                $user->profile->deleteCarLicence();
                $user->profile->car_licence = $user->profile->uploadCarLicence($this->request->file('car_licence'));
            }
            
            if($this->request->filled('password')) {
                $user->password = \Hash::make($this->request->input('password'));
                //                $user->password = null;
            }
			if($this->request->input('active') == 1)
			{
				$user->is_verified = 1;
			}
            $user->save();

            $user->profile->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $user;
    }
}
