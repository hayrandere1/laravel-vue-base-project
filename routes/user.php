<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\ForgotPasswordController;
use App\Http\Controllers\User\Auth\ResetPasswordController;
use App\Http\Controllers\User\Auth\ConfirmPasswordController;
use App\Http\Controllers\User\Auth\VerificationController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\UserRoleGroupController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\GroupController;
use App\Http\Controllers\User\PersonController;

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

Route::group(['middleware' => ['inertia']], function () {

// Authentication Routes...
    Route::get('login', [LoginController::class, 'showLoginForm'])
        ->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])
        ->name('logout');

    // Password Reset Routes...
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])
        ->name('password.update');

    // Confirm Password Routes...
    Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])
        ->name('password.confirm');
    Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

    // Email Verification Routes...
    Route::get('email/verify', [VerificationController::class, 'show'])
        ->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])
        ->name('verification.resend');

});

//, 'company','CheckSecurity'

Route::group(['middleware' => ['admin_or_manager_or_user', 'inertia', 'verified:user.verification.notice']], function () {
    Route::get('/', [HomeController::class, 'index'])
        ->name('home');

    Route::get('user_role_group/get_data', [UserRoleGroupController::class, 'getData'])
        ->name('user_role_group.getData');
    Route::resource('user_role_group', UserRoleGroupController::class);
    Route::post('user_role_group/download', [UserRoleGroupController::class, 'download'])
        ->name('user_role_group.download');

    Route::get('user/get_data', [UserController::class, 'getData'])
        ->name('user.getData');
    Route::resource('user', UserController::class);
    Route::post('user/download', [UserController::class, 'download'])
        ->name('user.download');

    Route::get('group/get_data', [GroupController::class, 'getData'])
        ->name('group.getData');
    Route::resource('group', GroupController::class);
    Route::post('group/download', [GroupController::class, 'download'])
        ->name('group.download');

    Route::get('person/get_data', [PersonController::class, 'getData'])
        ->name('person.getData');
    Route::resource('person', PersonController::class);
    Route::post('person/download', [PersonController::class, 'download'])
        ->name('person.download');

    Route::post('todolist', [HomeController::class, 'todolist'])
        ->name('todolist');
    Route::get('websocket', [HomeController::class, 'websocket'])
        ->name('websocket');
});

