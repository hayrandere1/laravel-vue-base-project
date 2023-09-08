<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\ConfirmPasswordController;
use App\Http\Controllers\Admin\Auth\VerificationController;
use App\Http\Controllers\Admin\AdminRoleGroupController;

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

    Route::group(['middleware' => ['auth:admin']], function () {
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

Route::group(['middleware' => ['auth:admin', 'inertia', 'verified:admin.verification.notice']], function () {
    Route::get('/', [HomeController::class, 'index'])
        ->name('home');

    Route::get('admin/get_data', [AdminController::class, 'getData'])
        ->name('admin.getData');

    Route::get('admin_role_group/get_data', [AdminRoleGroupController::class, 'getData'])
        ->name('admin_role_group.getData');
    Route::resource('admin_role_group', AdminRoleGroupController::class);

    Route::post('admin_role_group/download', [AdminRoleGroupController::class, 'download'])
        ->name('role_group.download');

    Route::post('todolist', [HomeController::class, 'todolist'])
        ->name('todolist');;

});

