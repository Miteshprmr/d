<?php

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

// Register the user.
Route::post('register', 'Auth\RegistrationController@register');

// User login.
Route::post('login', 'Auth\LoginController@login');

// Generate the access token using refresh token.
Route::post('refresh-token', 'Auth\LoginController@refreshToken');

Route::middleware('auth')->group(function () {

    // Logout the user.
    Route::post('logout', 'Auth\SessionController@logout');

});
