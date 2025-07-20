<?php

use App\Http\Controllers\Api\V1\TourController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/tours')->group(function () {
    Route::get('/prices', [TourController::class, 'prices']);
    Route::get('/', [TourController::class, 'index']);
    Route::get('/{id}', [TourController::class, 'show']);
    Route::get('/{id}/availability', [TourController::class, 'availability']);
});
