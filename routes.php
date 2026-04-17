<?php

use App\Controllers\BkashController;
use App\Controllers\IndexController;
use Core\Routing\Route;
use App\Controllers\NagadController;

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'site'], function () {
        Route::post('/health', [IndexController::class, 'health'], 'index.health');
    });
});

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'bkash'], function () {
        Route::post('/pay-now', [BkashController::class, 'paynow'], 'bkash.paynow');
        Route::get('/verify/{paymentId}', [BkashController::class, 'verify'], 'bkash.verify');
    });

    Route::group(['prefix' => 'nagad'], function () {
        Route::post('/pay-now', [NagadController::class, 'paynow'], 'nagad.paynow');
        Route::get('/verify/{paymentId}', [NagadController::class, 'verify'], 'nagad.verify');
    });
});
