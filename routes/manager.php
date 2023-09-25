<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\HomeController;
use App\Http\Controllers\Manager\Auth\LoginController;
use App\Http\Controllers\Manager\Auth\ForgotPasswordController;
use App\Http\Controllers\Manager\Auth\ResetPasswordController;
use App\Http\Controllers\Manager\Auth\ConfirmPasswordController;
use App\Http\Controllers\Manager\Auth\VerificationController;
use App\Http\Controllers\Manager\ManagerRoleGroupController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\UserController;

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

    Route::group(['middleware' => ['auth:manager']], function () {
        // Email Verification Routes...
        Route::get('email/verify', [VerificationController::class, 'show'])
            ->name('verification.notice');
        Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
            ->name('verification.verify');
        Route::post('email/resend', [VerificationController::class, 'resend'])
            ->name('verification.resend');
    });
});

//, 'company','CheckSecurity'

Route::group(['middleware' => ['auth:manager', 'inertia', 'verified:manager.verification.notice']], function () {
    Route::get('/', [HomeController::class, 'index'])
        ->name('home');

    Route::get('manager_role_group/get_data', [ManagerRoleGroupController::class, 'getData'])
        ->name('manager_role_group.getData');
    Route::resource('manager_role_group', ManagerRoleGroupController::class);
    Route::post('manager_role_group/download', [ManagerRoleGroupController::class, 'download'])
        ->name('manager_role_group.download');

    Route::get('manager/get_data', [ManagerController::class, 'getData'])
        ->name('manager.getData');
    Route::resource('manager', ManagerController::class);
    Route::post('manager/download', [ManagerController::class, 'download'])
        ->name('manager.download');

    Route::get('user/get_data', [UserController::class, 'getData'])
        ->name('user.getData');
    Route::resource('user', UserController::class);
    Route::post('user/download', [UserController::class, 'download'])
        ->name('user.download');

    Route::post('todolist', [HomeController::class, 'todolist'])
        ->name('todolist');
});

