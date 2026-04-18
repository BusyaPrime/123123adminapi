<?php

Route::group([
    'domain' => 'merchant.'.config('app.domain'),
    'namespace' => 'TAdmin',
    'as' => 'admin2.'
], function () {

    Route::group([
        'namespace' => 'Auth',
        'as' => 'auth.'
    ], function () {
        Route::get('login', 'LoginController@showLoginForm')->name('loginForm');
        Route::get('register', 'LoginController@showRegisterForm')->name('registerForm');
        Route::post('login', 'LoginController@login')->name('login');
        Route::post('register', 'LoginController@register')->name('register');
        Route::get('logout', 'LoginController@logout')->name('logout');
    });
	
	/*******06092022*******/
	Route::group([
		'prefix' => 'companies',
		'as' => 'companies.',
	], function () {
		Route::get('company-register/{number}', 'CompaniesController@register')->name('register');
		Route::get('thank-you', 'CompaniesController@thankyou')->name('thankyou');
		Route::get('web-thank-you', 'CompaniesController@webthankyou')->name('webthankyou');
		Route::post('store', 'CompaniesController@store')->name('store');
		Route::post('companystore', 'CompaniesController@companystore')->name('companystore');
	});
	
	/*******06092022*******/

    Route::group([
        'middleware' => ['auth', 'role:merchant', 'active', 'merchantCompany']
    ], function () {
        Route::get('home', function () {
            return redirect()->route('admin2.home');
        })->name('home.alias');

        Route::get('', 'HomeController@index')->name('home');
        Route::get('company', 'HomeController@company')->name('company');

        Route::group([
            'prefix' => 'profile',
            'as' => 'profile.'
        ], function () {
            Route::get('', 'ProfileController@edit')->name('edit');
            Route::put('', 'ProfileController@update')->name('update');
        });
//
//        Route::group([
//            'prefix' => 'tracking',
//            'as' => 'tracking.',
//            'middleware' => 'checkPermission:tracking',
//        ], function () {
//            Route::get('', 'RegionsController@tracking')->name('index');
//        });

        Route::group([
            'prefix' => 'cars',
            'as' => 'cars.',
        ], function () {
            Route::get('', 'CarsController@index')->name('index');
            Route::get('create', 'CarsController@create')->name('create');
            Route::post('store', 'CarsController@store')->name('store');
            Route::get('edit/{car}', 'CarsController@edit')->name('edit');
            Route::get('show/{car}', 'CarsController@show')->name('show');
            Route::put('update/{car}', 'CarsController@update')->name('update');
            Route::delete('destroy/{car}', 'CarsController@destroy')->name('destroy');

        });
		
		Route::group([
            'prefix' => 'users',
            'as' => 'users.',
        ], function () {
            Route::get('', 'UsersController@index')->name('index');
            Route::get('export', 'UsersController@export')->name('export');
            Route::get('offer/{user}', 'UsersController@offer')->name('offer');
            Route::get('create', 'UsersController@create')->name('create');
            Route::post('store', 'UsersController@store')->name('store');
            Route::get('edit/{user}', 'UsersController@edit')->name('edit');
            Route::get('show/{user}', 'UsersController@show')->name('show');
            Route::put('update/{user}', 'UsersController@update')->name('update');
            Route::delete('destroy/{user}', 'UsersController@destroy')->name('destroy');

            Route::post('push', 'UsersController@sendPush')->name('send-push');
            Route::delete('push/{user}/{notification}', 'UsersController@destroyPush')->name('destroy-push');

        });


        Route::group([
            'prefix' => 'bookings',
            'as' => 'bookings.',
        ], function () {
            Route::get('', 'BookingsController@index')->name('index');
			Route::get('export', 'BookingsController@export')->name('export');
            Route::get('show/{booking}', 'BookingsController@show')->name('show');
			Route::get('create', 'BookingsController@create')->name('create');
			Route::post('store', 'BookingsController@store')->name('store');
			Route::post('book', 'BookingsController@book')->name('book');
            Route::post('driver/{truckBooking}', 'BookingsController@changeState')->name('change-state');
            Route::get('show-user/{booking}', 'BookingsController@showUser')->name('show-user');
            Route::post('addPrice/{truckBooking}', 'BookingsController@additionalPrice')->name('addPrice');
        });


        Route::group([
            'prefix' => 'bookings-available',
            'as' => 'bookings-available.',
        ], function () {
            Route::get('', 'BookingsAvailableController@index')->name('index');
            Route::get('show/{booking}', 'BookingsAvailableController@show')->name('show');
            Route::post('driver/{truckBooking}', 'BookingsAvailableController@changeState')->name('change-state');
        });

        Route::group([
            'prefix' => 'tracking',
            'as' => 'tracking.',
        ], function () {
            Route::get('', 'TRegionsController@tracking')->name('index');
        });

        Route::group([
            'prefix' => 'transactions',
            'as' => 'transactions.',
        ], function () {
            Route::get('', 'TransactionsController@index')->name('index');
            Route::get('show/{transaction}', 'TransactionsController@show')->name('show');
            Route::get('balance', 'TransactionsController@companyBalance')->name('balance');
        });

        Route::group([
            'prefix' => 'statistics',
            'as' => 'statistics.',
        ], function () {
            Route::get('', 'StatisticsController@index')->name('index');
        });
//
//        Route::group([
//            'prefix' => 'statistics',
//            'as' => 'statistics.',
//            'middleware' => 'checkPermission:statistics',
//        ], function () {
//            Route::get('', 'StatisticsController@index')->name('index');
//            Route::get('chart/month/{model}', 'StatisticsController@month')->name('chart.month');
//            Route::get('chart/year/{year}', 'StatisticsController@year')->name('chart.year');
//        });
        Route::group([
            'prefix' => 'reviews',
            'as' => 'reviews.',
        ], function () {
            Route::get('', 'ReviewsController@index')->name('index');
        });

        Route::group([
            'prefix' => 'chat',
            'as' => 'chat.',
        ], function () {
            Route::get('bookings', 'ChatController@bookings')->name('index-bookings');
            Route::post('bookings', 'ChatController@sendMessageBooking')->name('send-bookings');
        });
    });
});
