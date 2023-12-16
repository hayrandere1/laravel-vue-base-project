<?php

use App\Http\Controllers\Api\V1\User\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\User\UserController;

Route::prefix('user')->name('user.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])
        ->name('home');
    Route::resource('user', UserController::class);
});
