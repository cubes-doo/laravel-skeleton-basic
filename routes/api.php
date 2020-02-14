<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::prefix('token')->group(function() {
    Route::post('', 'LoginController@token');
    Route::post('refresh', 'LoginController@tokenRefresh');
});

Route::post('forgot-password', 'ForgotPasswordController@forgotPassword');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
