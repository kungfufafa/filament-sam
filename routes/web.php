<?php

use App\Http\Controllers\OneSignalSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::post('/one-signal/subscriptions', [OneSignalSubscriptionController::class, 'store'])
        ->name('one-signal.subscriptions.store');
    Route::delete('/one-signal/subscriptions', [OneSignalSubscriptionController::class, 'destroy'])
        ->name('one-signal.subscriptions.destroy');
});
