<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Guest\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::redirect('admin', 'Admin');
Route::redirect('manager', 'Manager');
Route::redirect('user', 'User');


Route::group(['middleware' => ['inertia']], function () {
    Route::get('/', [HomeController::class, 'index'])
        ->name('home');
});
