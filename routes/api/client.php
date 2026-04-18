<?php

Route::group([
], function () {
    Route::get('', function () {
        return response()->json(['status' => 'ok']);
    })->name('root');

    Route::get('handbook/contacts', 'HandbookController@contacts')->name('handbook.contacts');
    Route::get('handbook/cancel-reasons', 'HandbookController@cancelReasons')->name('handbook.cancelReasons');
    Route::get('handbook/api-level', 'HandbookController@apiLevel')->name('handbook.api-level');
    Route::get('handbook/car-types', 'HandbookController@tCarTypes')->name('car-types');
    Route::get('handbook/sizes', 'HandbookController@sizes')->name('sizes');
    Route::get('handbook/partial-load-sizes', 'HandbookController@partialLoadSizes')->name('partial-load-sizes');
    Route::get('handbook/truck-types', 'HandbookController@carTypes')->name('truck-types');
    Route::get('handbook/truck-types-list', 'HandbookController@truckTypes')->name('truck-types-list');
    Route::get('handbook/regions', 'HandbookController@regions')->name('regions');
    Route::get('frontend/app-reviews', '\App\Http\Controllers\Admin\FrontendController@reviewsResource')->name('app-reviews');
    Route::post('sms-endpoint', 'SmsEndpointController@send')->name('sms-endpoint.send');
	Route::post('handbook/testorder', 'TruckBookingController@testorder')->name('handbook.testorder');
	Route::get('driverReminderNotification', 'TruckBookingController@driverReminderNotification')->name('driverReminderNotification');
	Route::get('confirmationOrders', 'TruckBookingController@confirmationOrders')->name('confirmationOrders');
	Route::get('app-settings', '\App\Http\Controllers\Admin\FrontendController@settings')->name('settings');
	Route::get('app-settings-lang', '\App\Http\Controllers\Admin\FrontendController@settingsByLang')->name('settingsByLang');
    Route::get('ai-price', 'HandbookController@aiPrice')->name('ai-price');

    Route::group([
        'prefix' => 'user-tracking',
        'as' => 'user-tracking.',
        'middleware' => 'apiBindUser'
    ], function () {
        Route::post('session/start', 'TrackingController@startSession')->name('session.start');/*->middleware('throttle:5,10');*/ // opened an app / visited website
        Route::post('session/end', 'TrackingController@endSession')->name('session.end'); // closed app / went out from page

        // if user logged in after creating session as anonymous, update existing session
        Route::post('session/identify', 'TrackingController@identifyUser')
            ->name('session.identify')
            ->middleware('throttle:10,1', 'apiAuth');

        Route::post('event/track', 'TrackingController@trackEvent')->name('event.track'); // one event
        // Route::post('events/batch', 'TrackingController@trackBatch'); // batch of events (if need offline)

        Route::post('funnel/order/start', 'TrackingController@startOrderFunnel')->name('funnel.order.start'); // started order filling
        Route::post('funnel/order/{funnelId}/step', 'TrackingController@orderFunnelStep')->name('funnel.order.step'); // endpoint passed
        Route::post('funnel/order/{funnelId}/complete', 'TrackingController@completeOrderFunnel')->name('funnel.order.complete'); // order created
        Route::post('funnel/order/{funnelId}/abandon', 'TrackingController@abandonOrderFunnel')->name('funnel.order.abandon'); // abandoned creating order
    });

    Route::get('offer/{lang}', 'HandbookController@offer')->name('api_offer');

    Route::get('payment/driver/{user}', 'HandbookController@paymentDriver')->name('payment.driver');
    Route::get('payment/company/{company}', 'HandbookController@paymentCompany')->name('payment.company');

    Route::group([
        'prefix' => 'handbook',
        'as' => 'handbook.',
    ], function () {
        Route::get('cargo-types', 'HandbookController@cargoTypes')->name('cargo-types');
        Route::get('load-types', 'HandbookController@loadTypes')->name('load-types');
        Route::get('ticket-themes', 'HandbookController@ticketThemes')->name('ticket-themes');
        Route::post('ticket', 'HandbookController@ticket')->name('ticket');
    });

    Route::group([
        'prefix' => 'articles',
        'as' => 'articles.',
    ], function () {
        Route::get('', 'ArticlesController@index')->name('index');
        Route::get('{article}', 'ArticlesController@show')->name('show');
    });

    Route::group([
        'prefix' => 'auth',
        'as' => 'auth.'
    ], function () {
        Route::post('login', 'AuthController@login')->name('login');
        Route::post('is-unique-login', 'AuthController@isUniqueLogin')->name('isUniqueLogin');
        Route::get('sms-timeout/{user}', 'AuthController@timeout')->name('timeout');
        Route::put('sms-resend/{user}', 'AuthController@resend')->name('resend');
        Route::put('login-verify/{user}', 'AuthController@loginVerify')->name('login-verify');
        Route::post('password-reset-request', 'AuthController@passwordResetRequest')->name('password-reset-request');
        // Route::post('password-reset-verify', 'AuthController@passwordResetVerify')->name('password-reset-verify');
        Route::put('password-reset', 'AuthController@passwordReset')->name('password-reset');
        Route::put('login-corporate/', 'AuthController@loginCorporate')->name('login-corporate');
        Route::post('sign-up-corporate', 'AuthController@signUpCorporate')->name('sign-up-corporate');
        Route::post('sign-up-personal', 'AuthController@signUpPersonal')->name('sign-up-personal');
        Route::put('login-token/{user}', 'AuthController@loginWithToken')->name('login-token');
		/*******05-09-2022******/
		Route::put('verify-user/{user}', 'AuthController@verifyUser')->name('verify-user');
		/***********************/
		/*******06-09-2022******/
		Route::put('merchant-help-request/{user}', 'AuthController@merchantHelpRequest')->name('merchant-help-request');
		/***********************/

        Route::group([
            'middleware' => 'apiAuth:driver,client,merchant'
        ], function () {
            Route::get('tokens', 'AuthController@tokens')->name('tokens');
            Route::post('logout/{userToken?}', 'AuthController@logout')->name('logout');
            Route::post('change-number', 'AuthController@changeNumber')->name('change-number');
            Route::put('change-number-verify/{user}',
                'AuthController@changeNumberVerify')->name('change-number-verify');
        });
    });

    Route::group([
        'middleware' => 'apiAuth:driver,client,merchant'
    ], function () {
        Route::group([
            'prefix' => 'profile',
            'as' => 'profile.',
        ], function () {
            Route::get('show', 'ProfileController@show')->name('show');
            Route::get('orders/count', 'ProfileController@getOrdersCount')->name('userOrdersCount');
            Route::get('offer', 'ProfileController@offer')->name('offer');
            Route::get('statistics', 'ProfileController@statistics')->name('statistics');
            Route::post('update', 'ProfileController@update')->name('update');
            Route::post('updateLocale', 'ProfileController@updateLocale')->name('updateLocale');
            Route::post('location', 'ProfileController@updateLocation')->name('update.location');
            Route::post('locationHeadless', 'ProfileController@updateLocationHeadless')->name('update.locationHeadless');
            Route::post('delete', 'ProfileController@deleteProfileRequest')->name('deleteProfileRequest');
            Route::post('upload-docs', 'ProfileController@driverUploadDocs')->name('upload-docs');
            
            Route::post('payment', 'ProfileController@getPaymentLink')->name('payment-link');
            
            Route::get('notifications', 'ProfileController@notifications')->name('notifications');
            Route::get('notifications/count', 'ProfileController@notificationsCount')->name('notifications.count');
            Route::get('messages/count', 'ProfileController@messagesCount')->name('messages.count');
            Route::get('notifications/mark-read', 'ProfileController@notificationsMarkRead')->name('notifications.mark-read');
            Route::delete('notifications/deleteNotification', 'ProfileController@deleteNotification')->name('notifications.deleteNotification');
            Route::delete('notifications/deleteAllNotifications', 'ProfileController@deleteAllNotifications')->name('notifications.deleteAllNotifications');
        });

        Route::group([
            'prefix' => 'profile',
            'as' => 'profile.',
        ], function () {
            Route::get('truck', 'ProfileController@showCar')->name('truck.show');
            Route::put('truck', 'ProfileController@updateCar')->name('truck.update');
            Route::get('car', 'ProfileController@showTCar')->name('car.show');
            Route::put('car', 'ProfileController@updateTCar')->name('car.update');
        });

        Route::post('admin-messages', 'AdminMessageController@sendMessage')->name('message');
        Route::get('admin-messages', 'AdminMessageController@messages')->name('messages');
        Route::get('admin-messages/count', 'AdminMessageController@messagesCount')->name('messages.count');

        Route::group([
            'prefix' => 'truck-booking',
            'as' => 'truck-booking.',
        ], function () {
            Route::get('', 'TruckBookingController@orders')->name('orders');
            Route::get('truckCompleted', 'TruckBookingController@truckCompleted')->name('truckCompleted');
            Route::get('details/{truckBooking}', 'TruckBookingController@getOrder')->name('details');
            Route::get('client', 'TruckBookingController@clientOrders')->name('clientOrders');
  	        Route::get('completed', 'TruckBookingController@getCompletedOrders')->name('getCompletedOrders');
            Route::get('orders', 'TruckBookingController@getBookingsInOrder')->name('getBookingsInOrder');
            Route::put('status/{truckBooking}', 'TruckBookingController@changeState')->name('changeState');
            // Route::get('orders', 'TruckBookingController@getBookingsInOrder')->name('getBookingsInOrder');
            Route::get('client/statistics', 'TruckBookingController@dashboardStatistics')->name('dashboardStatistics');
            Route::post('book', 'TruckBookingController@book')->name('book');
            Route::post('book-edit/{truckBooking}', 'TruckBookingController@bookEdit')->name('book-edit');
            Route::put('review/{truckBooking}', 'TruckBookingController@leaveReview')->name('review');
            Route::put('driver-review/{truckBooking}', 'TruckBookingController@leaveReviewDriver')->name('driver.review');
            Route::post('messages/{truckBooking}', 'TruckBookingController@sendMessage')->name('message');
            Route::get('messages/{truckBooking}', 'TruckBookingController@messages')->name('messages');
            Route::put('view/{truckBooking}', 'TruckBookingController@view')->name('view');
            Route::post('send-push', 'TruckBookingController@sendPush')->name('send-push');

            Route::get('price-offers/{booking}', 'TruckBookingController@getBookingOffers')->name('price-offers');
            Route::post('driver-offer-price/{truckBooking}', 'TruckBookingController@driverOfferPrice')->name('driver-offer-price');
            Route::post('client-offer-price/{booking}', 'TruckBookingController@clientOfferPrice')->name('client-offer-price');
            Route::put('client-accept-offer/{truckBooking}', 'TruckBookingController@clientAcceptDriverOffer')->name('client-accept-offer');
        });

        Route::group([
            'prefix' => 'car-booking',
            'as' => 'car-booking.',
        ], function () {
            Route::get('', 'TcarBookingController@orders')->name('orders');
            Route::get('details/{tcarBooking}', 'TcarBookingController@getOrder')->name('details');
            Route::get('client', 'TcarBookingController@clientOrders')->name('clientOrders');
            Route::get('orders', 'TcarBookingController@getBookingsInOrder')->name('getBookingsInOrder');
            Route::post('book', 'TcarBookingController@book')->name('book');
            Route::put('status/{tcarBooking}', 'TcarBookingController@changeState')->name('changeState');
            Route::put('review/{tcarBooking}', 'TcarBookingController@leaveReview')->name('review');
            Route::post('messages/{tcarBooking}', 'TcarBookingController@sendMessage')->name('message');
            Route::get('messages/{tcarBooking}', 'TcarBookingController@messages')->name('messages');
        });

        Route::group([
            'prefix' => 'address',
            'as' => 'address.',
        ], function () {
            Route::get('/recent', 'AddressController@getRecentAddress')->name('getRecentAddress');
            Route::get('/saved', 'AddressController@getSavedAddress')->name('getSavedAddress');
            Route::get('/saved/{address}', 'AddressController@getSavedAddress')->name('showSavedAddress');
            Route::post('/saved', 'AddressController@addSavedAddress')->name('addSavedAddress');
            Route::put('/saved/{address}', 'AddressController@editSavedAddress')->name('editSavedAddress');
            Route::delete('/saved/{address}', 'AddressController@deleteSavedAddress')->name('deleteSavedAddress');
        });

        Route::group([
            'prefix' => 'client-company',
            'as' => 'client-company.',
        ], function () {
            Route::get('show', 'CompanyController@show')->name('show');
            Route::get('users', 'CompanyController@users')->name('users');
            Route::get('transactions', 'CompanyController@transactions')->name('transactions');
            Route::post('user/add', 'CompanyController@addUser')->name('addUser');
            Route::get('user/{user}', 'CompanyController@userInfo')->name('userInfo');
            Route::post('user/{user}', 'CompanyController@editUser')->name('editUser');
            Route::delete('user/{user}', 'CompanyController@deleteUserFromCompany')->name('deleteUserFromCompany');
            Route::get('bookings', 'CompanyController@bookings')->name('bookings');
            Route::post('agree-terms', 'CompanyController@agreeWithTerms')->name('agreeWithTerms');
        });

    });
});
