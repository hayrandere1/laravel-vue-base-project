<?php

use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use Laravel\Passport\Http\Controllers\ClientController;
use Laravel\Passport\Http\Controllers\ScopeController;
use Laravel\Passport\Http\Controllers\PersonalAccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;
use App\Models\User;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['prefix' => 'v1/oauth/'], function () {
    Route::post('token', [AccessTokenController::class, 'issueToken']);
    Route::get('authorize', [AuthorizationController::class, 'authorize']);
    Route::post('authorize', [AuthorizationController::class, 'approve']);
});

Route::post('user_api_client', function (Request $request) {

    $user = User::where('username', $request->username)->where('is_active', true)->firstOrFail();
    if (\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        $data = [
            'client_id' => $user->apiClient->id,
            'client_secret' => $user->apiClient->secret
        ];
        return new JsonResponse(['data' => $data]);
    } else {
        return new JsonResponse(['message' => __('The provided password is incorrect.')], 401);
    }
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::prefix('v1')->group(base_path('routes/api/v1/rest.php'));
});
