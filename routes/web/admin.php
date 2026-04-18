<?php


Route::group([
    'domain' => 'admin.'.config('app.domain'),
    'namespace' => 'Admin',
    'as' => 'admin.'
], function () {

//    Route::get('map/test', 'HomeController@map')->name('map.test');

    Route::get('booking/excel', 'BookingsController@excelImport')->name('');

    Route::group([
        'prefix' => 'frontend',
        'as' => 'frontend.'
    ], function () {
        Route::post('uploadWebsiteFiles', 'FrontendController@uploadWebsiteFiles')->name('uploadWebsiteFiles');
        Route::post('deleteWebsiteFile', 'FrontendController@deleteWebsiteFile')->name('deleteWebsiteFile');
        Route::post('update', 'FrontendController@updateFrontendData')->name('update');
    });

    Route::group([
        'namespace' => 'Auth',
        'as' => 'auth.'
    ], function () {
        Route::get('login', 'LoginController@showLoginForm')->name('loginForm');
        Route::post('login', 'LoginController@login')->name('login');
        Route::get('logout', 'LoginController@logout')->name('logout');
    });

    Route::group([
        'middleware' => ['auth', 'role:admin', 'active'] //, 'checkPermission:admins'
    ], function () {
        Route::get('home', function () {
            return redirect()->route('admin.home');
        })->name('home.alias');

        Route::get('', 'HomeController@index')->name('home');

        Route::group([
            'prefix' => 'profile',
            'as' => 'profile.'
        ], function () {
            Route::get('', 'ProfileController@edit')->name('edit');
            Route::put('', 'ProfileController@update')->name('update');
        });

        Route::group([
            'prefix' => 'admins',
            'as' => 'admins.',
            'middleware' => 'checkPermission:admins',
        ], function () {
            Route::get('', 'AdminsController@index')->name('index');
            Route::get('create', 'AdminsController@create')->name('create');
            Route::post('store', 'AdminsController@store')->name('store');
            Route::get('edit/{user}', 'AdminsController@edit')->name('edit');
            Route::put('update/{user}', 'AdminsController@update')->name('update');
            Route::delete('destroy/{user}', 'AdminsController@destroy')->name('destroy');

            Route::group([
                'prefix' => 'roles',
                'as' => 'roles.',
            ], function () {
                Route::get('', 'AdminRolesController@index')->name('index');
                Route::get('create', 'AdminRolesController@create')->name('create');
                Route::post('store', 'AdminRolesController@store')->name('store');
                Route::get('edit/{role}', 'AdminRolesController@edit')->name('edit');
                Route::put('update/{role}', 'AdminRolesController@update')->name('update');
                Route::delete('destroy/{role}', 'AdminRolesController@destroy')->name('destroy');
            });
        });

        Route::group([
            'prefix' => 'regions',
            'as' => 'regions.',
            'middleware' => 'checkPermission:regions',
        ], function () {
            Route::get('', 'RegionsController@index')->name('index');
            Route::get('distances', 'RegionsController@distances')->name('distances');
            Route::get('manageTariffs', 'RegionsController@manageTariffs')->name('manageTariffs');
            Route::get('changeByPrice', 'RegionsController@changeByPriceView')->name('changeByPrice');
            Route::put('updateTariffByPrice', 'RegionsController@updateTariffByPrice')->name('updateTariffByPrice');
            Route::put('manageTariffsUpdate', 'RegionsController@manageTariffsUpdate')->name('manageTariffsUpdate');
            Route::get('tariffsExport', 'RegionsController@export')->name('tariffsExport');
            Route::get('tariffsExportAll', 'RegionsController@exportAll')->name('tariffsExportAll');
            Route::put('distances/update', 'RegionsController@updateDistances')->name('distances.updateDistances');
            Route::get('tariffs/filterTariffs', 'RegionsController@filterTariffs')->name('filterTariffs');
            Route::get('edit/{region}', 'RegionsController@edit')->name('edit');
            Route::put('update/{region}', 'RegionsController@update')->name('update');
        });
        
        Route::group([
            'prefix' => 'system',
            'as' => 'system.',
            'middleware' => 'checkPermission:system',
        ], function () {
            Route::get('', 'SystemController@index')->name('index');
            Route::get('logs', 'SystemController@systemLogs')->name('logs');
            Route::get('clear-logs', 'SystemController@clearSystemLogs')->name('clearLogs');
            Route::get('confirmation-codes', 'SystemController@confirmationCodes')->name('confirmationCodes');
        });
        
        Route::group([
            'prefix' => 'frontend',
            'as' => 'frontend.',
            'middleware' => 'checkPermission:frontend',
        ], function () {
            Route::get('website', 'FrontendController@websiteView')->name('website');
            Route::get('update-website', 'FrontendController@websiteUpdate')->name('website-update'); 
            Route::get('app-reviews', 'FrontendController@appReviewsView')->name('app-reviews'); 
            Route::get('offer', 'FrontendController@offer')->name('offer');
            Route::post('offer/store', 'FrontendController@storeOffer')->name('store-offer');
            Route::get('form/{review?}', 'FrontendController@appReviewForm')->name('app-reviews.form'); 
            Route::post('add/{review?}', 'FrontendController@storeAppReview')->name('app-reviews.add'); 
            Route::get('show/{review}', 'FrontendController@showAppReview')->name('app-reviews.show');  
            Route::delete('delete/{review}', 'FrontendController@deleteAppReview')->name('app-reviews.delete'); 
        });
        
        Route::group([
            'prefix' => 'rate-history',
            'as' => 'rate-history.',
            'middleware' => 'checkPermission:rates_history',
        ], function () {
            Route::get('', 'CarTypeRatesHistoryController@index')->name('index');
        });

        Route::group([
            'prefix' => 'tracking',
            'as' => 'tracking.',
            'middleware' => 'checkPermission:tracking',
        ], function () {
            Route::get('', 'RegionsController@tracking')->name('index');
        });

        Route::group([
            'prefix' => 'users',
            'as' => 'users.',
            'middleware' => 'checkPermission:users',
        ], function () {
            Route::get('', 'UsersController@index')->name('index');
            Route::get('export', 'UsersController@export')->name('export');
            Route::get('offer/{user}', 'UsersController@offer')->name('offer');
            Route::get('create', 'UsersController@create')->name('create');
            Route::post('store', 'UsersController@store')->name('store');
            Route::get('statistics', 'UsersController@statistics')->name('statistics');
            Route::get('edit/{user}', 'UsersController@edit')->name('edit');
            Route::get('show/{user}', 'UsersController@show')->name('show');
            Route::put('update/{user}', 'UsersController@update')->name('update');
            Route::delete('destroy/{user}', 'UsersController@destroy')->name('destroy');

            Route::post('push', 'UsersController@sendPush')->name('send-push');
            Route::delete('push/{user}/{notification}', 'UsersController@destroyPush')->name('destroy-push');

        });

        Route::group([
            'prefix' => 'car-types',
            'as' => 'car-types.',
            'middleware' => 'checkPermission:car-types',
        ], function () {
            Route::get('', 'CarTypesController@index')->name('index');
            Route::get('create', 'CarTypesController@create')->name('create');
            Route::get('serving_prices', 'CarTypesController@servingPrices')->name('servingPrices');
            Route::put('update_serving_prices', 'CarTypesController@updateServingPrices')->name('updateServingPrices');
            Route::post('store', 'CarTypesController@store')->name('store');
            Route::get('edit/{carType}', 'CarTypesController@edit')->name('edit');
            Route::get('restrictions/{carType}', 'CarTypesController@showRestrictions')->name('restrictions');
            Route::put('update/{carType}', 'CarTypesController@update')->name('update');
            Route::put('updateRestrictions/{carType}', 'CarTypesController@updateRestriction')->name('updateRestrictions');
            Route::delete('destroy/{carType}', 'CarTypesController@destroy')->name('destroy');

            Route::group([
                'prefix' => 'rates',
                'as' => 'rates.',
            ], function () {
                Route::get('edit/{carType}', 'CarTypeRatesController@edit')->name('edit');
                Route::post('update/{carType}', 'CarTypeRatesController@update')->name('update');
            });
        });

        Route::group([
            'prefix' => 'sizes',
            'as' => 'sizes.',
            'middleware' => 'checkPermission:sizes',
        ], function () {
            Route::get('', 'SizesController@index')->name('index');
            Route::get('create', 'SizesController@create')->name('create');
            Route::post('store', 'SizesController@store')->name('store');
            Route::get('edit/{size}', 'SizesController@edit')->name('edit');
            Route::put('update/{size}', 'SizesController@update')->name('update');
            Route::delete('destroy/{size}', 'SizesController@destroy')->name('destroy');
        });

        Route::group([
            'prefix' => 'cargo-types',
            'as' => 'cargo-types.',
            'middleware' => 'checkPermission:cargo-types',
        ], function () {
            Route::get('', 'CargoTypesController@index')->name('index');
            Route::get('create', 'CargoTypesController@create')->name('create');
            Route::post('store', 'CargoTypesController@store')->name('store');
            Route::get('edit/{cargoType}', 'CargoTypesController@edit')->name('edit');
            Route::put('update/{cargoType}', 'CargoTypesController@update')->name('update');
            Route::delete('destroy/{cargoType}', 'CargoTypesController@destroy')->name('destroy');
        });

        Route::group([
            'prefix' => 'cancel-reasons',
            'as' => 'cancel-reasons.',
            'middleware' => 'checkPermission:cancel_reasons',
        ], function () {
            Route::get('', 'CancelReasonsController@index')->name('index');
            Route::get('create', 'CancelReasonsController@create')->name('create');
            Route::post('store', 'CancelReasonsController@store')->name('store');
            Route::get('edit/{cancelReason}', 'CancelReasonsController@edit')->name('edit');
            Route::put('update/{cancelReason}', 'CancelReasonsController@update')->name('update');
            Route::delete('destroy/{cancelReason}', 'CancelReasonsController@destroy')->name('destroy');
        });

        Route::group([
            'prefix' => 'ticket-themes',
            'as' => 'ticket-themes.',
            'middleware' => 'checkPermission:ticket_themes',
        ], function () {
            Route::get('', 'TicketThemesController@index')->name('index');
            Route::get('create', 'TicketThemesController@create')->name('create');
            Route::post('store', 'TicketThemesController@store')->name('store');
            Route::get('edit/{ticketTheme}', 'TicketThemesController@edit')->name('edit');
            Route::put('update/{ticketTheme}', 'TicketThemesController@update')->name('update');
            Route::delete('destroy/{ticketTheme}', 'TicketThemesController@destroy')->name('destroy');
        });

        Route::group([
            'prefix' => 'tickets',
            'as' => 'tickets.',
            'middleware' => 'checkPermission:tickets',
        ], function () {
            Route::get('', 'TicketsController@index')->name('index');
            Route::get('edit/{ticket}', 'TicketsController@edit')->name('edit');
            Route::put('update/{ticket}', 'TicketsController@update')->name('update');
            Route::delete('destroy/{ticket}', 'TicketsController@destroy')->name('destroy');
        });

        Route::group([
            'prefix' => 'commission-rates',
            'as' => 'commission-rates.',
            'middleware' => 'checkPermission:commission-rates',
        ], function () {
            Route::get('', 'CommissionRatesController@index')->name('index');
            Route::get('create', 'CommissionRatesController@create')->name('create');
            Route::post('store', 'CommissionRatesController@store')->name('store');
            Route::get('edit/{rate}', 'CommissionRatesController@edit')->name('edit');
            Route::put('update/{rate}', 'CommissionRatesController@update')->name('update');
            Route::delete('destroy/{rate}', 'CommissionRatesController@destroy')->name('destroy');
        });
        
        Route::group([
            'prefix' => 'company_priorities',
            'as' => 'company_priorities.',
            'middleware' => 'checkPermission:company_priorities',
        ], function () {
            Route::get('', 'CompanyPriorities@index')->name('index');
            Route::get('create', 'CompanyPriorities@create')->name('create');
            Route::post('store', 'CompanyPriorities@store')->name('store');
            Route::get('edit/{priority}', 'CompanyPriorities@edit')->name('edit');
            Route::put('update/{priority}', 'CompanyPriorities@update')->name('update');
            Route::delete('destroy/{priority}', 'CompanyPriorities@destroy')->name('destroy');
        });

        Route::group([
            'prefix' => 'load-types',
            'as' => 'load-types.',
            'middleware' => 'checkPermission:load-types',
        ], function () {
            Route::get('', 'LoadTypesController@index')->name('index');
            Route::get('create', 'LoadTypesController@create')->name('create');
            Route::post('store', 'LoadTypesController@store')->name('store');
            Route::get('edit/{loadType}', 'LoadTypesController@edit')->name('edit');
            Route::put('update/{loadType}', 'LoadTypesController@update')->name('update');
            Route::delete('destroy/{loadType}', 'LoadTypesController@destroy')->name('destroy');
        });


        Route::group([
            'prefix' => 'seasons',
            'as' => 'seasons.',
            'middleware' => 'checkPermission:seasons',
        ], function () {
            Route::get('', 'SeasonsController@index')->name('index');
            Route::get('create', 'SeasonsController@create')->name('create');
            Route::post('store', 'SeasonsController@store')->name('store');
            Route::get('edit/{season}', 'SeasonsController@edit')->name('edit');
            Route::put('update/{season}', 'SeasonsController@update')->name('update');
            Route::delete('destroy/{season}', 'SeasonsController@destroy')->name('destroy');
        });

        Route::group([
            'prefix' => 'cars',
            'as' => 'cars.',
            'middleware' => 'checkPermission:cars',
        ], function () {
            Route::get('', 'CarsController@index')->name('index');
            Route::get('export', 'CarsController@export')->name('export');
            Route::get('create', 'CarsController@create')->name('create');
            Route::post('store', 'CarsController@store')->name('store');
            Route::get('edit/{car}', 'CarsController@edit')->name('edit');
            Route::get('show/{car}', 'CarsController@show')->name('show');
            Route::put('update/{car}', 'CarsController@update')->name('update');
            Route::delete('destroy/{car}', 'CarsController@destroy')->name('destroy');
            Route::delete('destroyLicence/{car}', 'CarsController@destroyLicence')->name('destroyLicence');
            Route::delete('destroyCarLicence/{car}', 'CarsController@destroyCarLicence')->name('destroyCarLicence');

            Route::post('push', 'CarsController@sendPush')->name('send-push');
            Route::delete('push/{user}/{notification}', 'CarsController@destroyPush')->name('destroy-push');
        });

        Route::group([
            'prefix' => 'companies',
            'as' => 'companies.',
            'middleware' => 'checkPermission:companies',
        ], function () {
            Route::get('', 'CompaniesController@index')->name('index');
            Route::get('export', 'CompaniesController@export')->name('export');
            Route::get('create', 'CompaniesController@create')->name('create');
            Route::post('store', 'CompaniesController@store')->name('store');
            Route::get('edit/{company}', 'CompaniesController@edit')->name('edit');
            Route::get('show/{company}', 'CompaniesController@show')->name('show');
            Route::get('show-orders/{company}', 'CompaniesController@showOrders')->name('show-orders');
            Route::put('update/{company}', 'CompaniesController@update')->name('update');
            Route::delete('destroy/{company}', 'CompaniesController@destroy')->name('destroy');
        });

        Route::group([
            'prefix' => 'bookings',
            'as' => 'bookings.',
            'middleware' => 'checkPermission:bookings',
        ], function () {
            Route::get('', 'BookingsController@index')->name('index');
            Route::get('export', 'BookingsController@export')->name('export');
            Route::get('show/{booking}', 'BookingsController@show')->name('show');
            Route::get('show/{booking}/views-preview', 'BookingsController@viewsPreview')->name('views-preview');
            Route::get('show/{booking}/price-offers-preview', 'BookingsController@priceOffersPreview')->name('price-offers-preview');
            Route::get('create', 'BookingsController@create')->name('create');
            Route::get('driver-price-offers', 'BookingsController@driverBookingOffers')->name('driverPriceOffers');
            Route::get('driver-price-offers/show/{driverOffer}', 'BookingsController@showDriverOffer')->name('showDriverOffer');
            Route::post('store', 'BookingsController@store')->name('store');
            Route::post('book', 'BookingsController@book')->name('book');
            Route::post('driver/{truckBooking}', 'BookingsController@changeState')->name('change-state');
            Route::post('change-status/{booking}', 'BookingsController@changeStatus')->name('change-status');
            Route::post('addPrice/{truckBooking}', 'BookingsController@additionalPrice')->name('addPrice');
            Route::post('editPrice/{truckBooking}', 'BookingsController@editPrice')->name('editPrice');
            Route::post('cancel/{truckBooking}', 'BookingsController@cancel')->name('cancel');
            Route::get('statistics/export/year', 'BookingsController@yearlyStatistics')->name('statistics-yearly');
            Route::get('statistics/bookingsByDrivers', 'BookingsController@bookingsByDrivers')->name('bookings-by-drivers');
        });

        Route::group([
            'prefix' => 'contacts',
            'as' => 'contacts.',
            'middleware' => 'checkPermission:contacts',
        ], function () {
            Route::get('', 'ContactsController@index')->name('index');
            Route::put('update', 'ContactsController@update')->name('update');
        });

        Route::group([
            'prefix' => 'transactions',
            'as' => 'transactions.',
            'middleware' => 'checkPermission:transactions',
        ], function () {
            Route::get('', 'TransactionsController@index')->name('index');
            Route::get('export', 'TransactionsController@export')->name('export');
            Route::get('show/{transaction}', 'TransactionsController@show')->name('show');
            Route::post('store-company', 'TransactionsController@storeCompany')->name('store-company');
            Route::post('store-user', 'TransactionsController@storeUser')->name('store-user');
            Route::delete('destroy/{transaction}', 'TransactionsController@destroy')->name('destroy');

            Route::get('debts/companies', 'CompaniesController@debts')->name('debts.companies');
            Route::get('debts/users', 'CarsController@debts')->name('debts.users');

            Route::get('debts/companies/export', 'CompaniesController@debtsExport')->name('debts.companies.export');
            Route::get('debts/users/export', 'CarsController@debtsExport')->name('debts.users.export');

            Route::get('import/form', 'TransactionsController@importForm')->name('import-form');
            Route::post('import/show', 'TransactionsController@importShow')->name('import-show');
            Route::post('import/excel', 'TransactionsController@importExcel')->name('import-excel');
        });

        Route::group([
            'prefix' => 'statistics',
            'as' => 'statistics.',
            'middleware' => 'checkPermission:statistics',
        ], function () {
            Route::get('', 'StatisticsController@index')->name('index');

            Route::get('bookings/year', 'StatisticsController@bookingsYearDetails')->name('bookings.year');
            Route::get('bookings/month', 'StatisticsController@bookingsMonthDetails')->name('bookings.month');
            Route::get('users/year', 'StatisticsController@usersYearDetails')->name('users.year');
            Route::get('users/month', 'StatisticsController@usersMonthDetails')->name('users.month');
            Route::get('sums/year', 'StatisticsController@sumsYearDetails')->name('sums.year');
            Route::get('sums/month', 'StatisticsController@sumsMonthDetails')->name('sums.month');

            Route::get('regions/graphic', 'StatisticsController@regionsDetails')->name('regions.graphic');
            Route::get('regions/users', 'StatisticsController@usersByRegions')->name('regions.users');
            Route::get('directions/graphic', 'StatisticsController@directionsDetails')->name('directions.graphic');
            Route::get('directions/graphic/export', 'StatisticsController@directionsDetailsExport')->name('directions.graphic.export');
            Route::get('accounts/drivers', 'StatisticsController@accountsDriversDetails')->name('accounts.drivers');
            Route::get('accounts/users', 'StatisticsController@accountsUsersDetails')->name('accounts.users');
            Route::get('avg-time', 'StatisticsController@avgTime')->name('avg-time');
            Route::get('cancel-reasons', 'StatisticsController@cancelReasonsDetails')->name('cancel-reasons');

            Route::get('accounts/drivers/export', 'StatisticsController@accountsDriversDetailsExport')->name('accounts.drivers.export');
            Route::get('accounts/users/export', 'StatisticsController@accountsUsersDetailsExport')->name('accounts.users.export');
            Route::get('avg-time/export', 'StatisticsController@avgTimeExport')->name('avg-time.export');
            Route::get('cancel-reasons/export', 'StatisticsController@cancelReasonsDetailsExport')->name('cancel-reasons.export');

//            Route::get('chart/month/{model}', 'StatisticsController@month')->name('chart.month');
//            Route::get('chart/year/{year}', 'StatisticsController@year')->name('chart.year');
        });

        Route::group([
            'prefix' => 'reviews',
            'as' => 'reviews.',
            'middleware' => 'checkPermission:reviews',
        ], function () {
            Route::get('', 'ReviewsController@index')->name('index');
            Route::get('delete/{booking}', 'ReviewsController@delete')->name('delete');
        });

        Route::group([
            'prefix' => 'chat',
            'as' => 'chat.',
            'middleware' => 'checkPermission:chat',
        ], function () {
            Route::get('', 'ChatController@index')->name('index');
            Route::post('', 'ChatController@sendMessage')->name('send');

            Route::get('bookings', 'ChatController@bookings')->name('index-bookings');
            Route::post('bookings', 'ChatController@sendMessageBooking')->name('send-bookings');
        });

        Route::group([
            'prefix' => 'articles',
            'as' => 'articles.',
            'middleware' => 'checkPermission:articles',
        ], function () {
            Route::get('', 'ArticlesController@index')->name('index');
            Route::get('create', 'ArticlesController@create')->name('create');
            Route::post('store', 'ArticlesController@store')->name('store');
            Route::get('edit/{article}', 'ArticlesController@edit')->name('edit');
            Route::put('update/{article}', 'ArticlesController@update')->name('update');
            Route::delete('destroy/{article}', 'ArticlesController@destroy')->name('destroy');
            Route::delete('destroy-photo/{article}', 'ArticlesController@destroyImage')->name('destroy-photo');
        });
		
		Route::group([
            'prefix' => 'appversions',
            'as' => 'appversions.',
            'middleware' => 'checkPermission:companies',
        ], function () {
            Route::get('', 'AppVersionsController@index')->name('index');
            Route::get('export', 'AppVersionsController@export')->name('export');
            Route::get('create', 'AppVersionsController@create')->name('create');
            Route::post('store', 'AppVersionsController@store')->name('store');
            Route::get('edit/{company}', 'AppVersionsController@edit')->name('edit');
            Route::get('show/{company}', 'AppVersionsController@show')->name('show');
            Route::get('show-orders/{company}', 'AppVersionsController@showOrders')->name('show-orders');
            /* Route::post('update/{company}', 'AppVersionsController@update')->name('update');
            Route::delete('destroy/{company}', 'AppVersionsController@destroy')->name('destroy'); */
			Route::post('update', 'AppVersionsController@update')->name('update');
            Route::delete('destroy', 'AppVersionsController@destroy')->name('destroy');
			Route::post('filterdata', 'AppVersionsController@filterData')->name('filterdata');
        });
		
		Route::group([
            'prefix' => 'merchanthelprequests',
            'as' => 'merchanthelprequests.',
            'middleware' => 'checkPermission:companies',
        ], function () {
            Route::get('', 'MerchantHelpRequestsController@index')->name('index');
            Route::post('edit/{request}', 'MerchantHelpRequestsController@update')->name('edit');
            Route::delete('delete/{request}', 'MerchantHelpRequestsController@destroy')->name('delete');
        });

        Route::group([
            'prefix' => 'deleterequests',
            'as' => 'deleterequests.',
            'middleware' => 'checkPermission:users',
        ], function () {
            Route::get('', 'UserDeleteRequestsController@index')->name('index');
            Route::post('deleteuser/{delete_req}', 'UserDeleteRequestsController@deleteUser')->name('deleteuser');
			Route::post('declineuser', 'UserDeleteRequestsController@declineUser')->name('declineuser');
        });

        Route::group([
            'prefix' => 'dashboards',
            'as' => 'dashboards.',
            'middleware' => 'checkPermission:statistics',
        ], function () {
            Route::get('', 'DashboardController@index')->name('index');
            Route::get('gmv', 'DashboardController@index')->name('gmv');
            Route::get('funnel', 'DashboardController@funnel')->name('funnel');
        });

//        Route::group([
//            'prefix' => 'sockets',
//            'as' => 'sockets.',
//        ], function () {
//            Route::get('', 'SocketsController@index')->name('index');
//            Route::get('restart', 'SocketsController@restart')->name('restart');
//        });
    });
});
