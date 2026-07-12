<?php

use App\Http\Controllers\Api\ShippingRateController;
use Illuminate\Support\Facades\Route;

Route::post('/{team:slug}/shipping/rates', [ShippingRateController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('api.shipping.rates');
