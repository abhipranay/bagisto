<?php

Route::group(['middleware' => ['web']], function () {
    Route::prefix('stripe/standard')->group(function () {
        
        Route::get('/success', 'Webkul\Stripe\Http\Controllers\StandardController@success')->name('stripe.standard.success');

        Route::get('/cancel', 'Webkul\Stripe\Http\Controllers\StandardController@cancel')->name('stripe.standard.cancel');
        
	Route::get('/pay', 'Webkul\Stripe\Http\Controllers\StandardController@details')->name('stripe.standard.pay');

        Route::post('/pay', 'Webkul\Stripe\Http\Controllers\StandardController@doPayment')->name('stripe.standard.doPayment');
    });
});
