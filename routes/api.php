<?php

use App\Http\Controllers\Api\V1\TourController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')
    ->name('api.v1.')
    ->middleware(['localized'])
    ->group(function () {

        Route::prefix('tours')
            ->name('tours.')
            ->controller(TourController::class)
            ->group(function () {

                Route::get('prices', 'prices')->name('prices');
                Route::get('/', 'index')->name('index');
                Route::get('{id}', 'show')->name('show');
                Route::get('{id}/availability', 'availability')->name('availability');
            });
    });
