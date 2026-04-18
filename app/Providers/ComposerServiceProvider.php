<?php

namespace App\Providers;

use App\Domain\AdminMessages\Models\AdminMessage;
use App\Domain\Cars\Models\Car;
use App\Domain\MerchantHelpRequests\Models\MerchantHelpRequest;
use App\Domain\UserDeleteRequests\Model\UserDeleteRequest;
use App\Domain\Users\Models\User;
use Illuminate\Support\ServiceProvider;
use Request;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            $current_route_name = Request::segment(1);
            $newMessages = AdminMessage::whereNotNull('user_id')
                ->where('read', 0)
                ->count();
            $newCars = Car::where('moderated', 0)->where('active', 1)
                ->count();
            /**
             * @var $view \Illuminate\View\View|\Illuminate\Contracts\View\Factory
             */
            $userPermissions = [];
            $user = Request::user();
            if ($user) {
                $adminRole = $user->adminRole;
                if ($adminRole) {
                    $userPermissions = json_decode($user->adminRole->permissions, true);
                }
			$view->with('user_data', $user);
            }
			$merchanthelpcount = MerchantHelpRequest::where('is_active', '=', 1)->count();
			$userDeleteRequestCount = UserDeleteRequest::where('status', UserDeleteRequest::STATUS_PENDING)->count();
			$newmerchants = User::where('role', '=', User::ROLE_MERCHANT)->where('active', '=', 0)->where('is_verified', '=', 0)->where('is_external', '=', 1)->count();


            $view->with('newMessages', $newMessages);
            $view->with('current_route_name', $current_route_name);
            $view->with('user_permissions', $userPermissions);
            $view->with('new_cars', $newCars);
            $view->with('merchant_help_count', $merchanthelpcount);
            $view->with('user_delete_request_count', $userDeleteRequestCount);
            $view->with('new_merchants', $newmerchants);
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
