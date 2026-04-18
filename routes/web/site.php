<?php

Route::group([
    'domain' => 'bc.'.config('app.domain'),
    'as' => 'bc.'
], function () {
    Route::get('/', 'OctoController@index')->name('home');
    Route::get('/account-deletion', function(){
        return View::make('admin.account_deletion');
    })->name('account-deletion');
});

Route::group([
    'domain' => config('app.domain'),
    'as' => 'site.'
], function () {
    Route::get('/', 'OctoController@index')->name('home');

    // Route::get('offer', function() {
    //   return view('documents.offer');
    // });

    Route::get('offer', 'OctoController@offer')->name('offer');

    Route::get('privacypolicy', 'OctoController@offer')->name('privacypolicy');

    // Route::fallback(function () {
    //     return view('welcome');
    // });

    Route::fallback('OctoController@index')->name('fallback');

    Route::post('octo/notify', 'OctoController@notify')->name('octo.notify');
    Route::get('octo/success', 'OctoController@success')->name('octo.success');
});
