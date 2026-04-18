<?php


namespace App\Domain\Cars\Jobs;


use App\Domain\Cars\Models\Car;
use App\Domain\Cars\Requests\StoreCarRequest;
use App\Domain\Cars\Requests\UpdateCarRequest;
use App\Domain\Users\Models\User;
use App\Domain\PushNotifications\Jobs\SendPushJob;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateCarJob
{
    use Dispatchable, Queueable;

    /**
     * @var UpdateCarRequest | StoreCarRequest
     */
    protected $request;

    protected $user;

    /**
     * UpdateCarJob constructor.
     * @param  UpdateCarRequest | StoreCarRequest  $request
     * @param  User  $user
     */
    public function __construct($request, User $user)
    {
        $this->request = $request;
        $this->user = $user;
    }


    /**
     * Execute the job.
     *
     * @return Car
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $car = $this->user->car ?? new Car();

            $car->car_type_id = $this->request->input('car_type_id', 0);
            $car->model = $this->request->input('model');
            $car->color = $this->request->input('color');
            $car->number = $this->request->input('number');
            $car->trailer_number = $this->request->input('trailer_number');
            $car->max_weight = $this->request->input('max_weight');
            $car->dimension_x = $this->request->input('dimension_x');
            $car->dimension_y = $this->request->input('dimension_y');
            $car->dimension_z = $this->request->input('dimension_z');
            $car->can_pack = $this->request->input('can_pack', 0);
            $car->can_provide_loader = $this->request->input('can_provide_loader', 0);
            $car->active = $this->request->input('active', 1);

            if($car->moderated == 0 && $this->request->input('moderated') == '1') {
                $this->request->merge([
                        'message' => 'messages.push_messages.car_moderated',
                        'user_id' => $this->user->id
                    ]);
                    dispatch(new SendPushJob($this->request, 'drivers'));
            }

            $car->moderated = $this->request->input('moderated', 0);
            $this->user->car()->save($car);


            $car->cargoTypes()->detach();
            $car->cargoTypes()->attach($this->request->input('cargo_types', []));

            $load_types = [];
            if ($this->request->filled('load_type')) {
                $load_types = [$this->request->input('load_type', null)];
            }
            $car->loadTypes()->detach();
            $car->loadTypes()->attach($load_types);

            if($this->request->user() && ($this->request->user()->isAdmin() || $this->request->user()->role == 'merchant')) {
                if ($this->user->profile) {
                    $this->user->profile->company_id = $this->request->input('company_id', null);
                    $this->user->profile->save();
                }
            }
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $car;
    }
}
