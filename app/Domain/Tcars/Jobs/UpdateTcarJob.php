<?php


namespace App\Domain\Tcars\Jobs;


use App\Domain\TCars\Models\TCar;
use App\Domain\TCars\Requests\StoreTcarRequest;
use App\Domain\TCars\Requests\UpdateTcarRequest;
use App\Domain\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateTcarJob
{

    use Dispatchable, Queueable;

    /**
     * @var UpdateTcarRequest | StoreTcarRequest
     */
    protected $request;

    protected $user;

    /**
     * UpdateCarJob constructor.
     * @param  UpdateTcarRequest | StoreTcarRequest  $request
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
     * @return Tcar
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $car = $this->user->tcar ?? new Tcar();

            $car->tcar_type_id = $this->request->input('car_type_id');
            $car->model = $this->request->input('model');
            $car->color = $this->request->input('color');
            $car->number = $this->request->input('number');
            $car->peoples = $this->request->input('peoples', 0);
            $car->ac = $this->request->input('ac', 0);
            $car->kids_seat = $this->request->input('kids_seat', 0);

            $this->user->tcar()->save($car);

            if($this->request->user() && $this->request->user()->isAdmin()) {
                $companies = [];
                if ($this->request->filled('company_id')) {
                    $companies = [$this->request->input('company_id', null)];
                }
                $car->active = $this->request->input('active', 0);
                $car->save();
                $car->companies()->detach();
                $car->companies()->attach($companies);
            }


        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $car;
    }
}
