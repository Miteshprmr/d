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
    
Route::prefix('users')->group(function () {

    // Get users with their total account balance.
    Route::get('', 'UserController@index')->name('users.index');

    // Get single user.
    Route::get('/{user}', 'UserController@show')->name('users.show');
});

Route::middleware('auth')->group(function () {

    // Logout the user.
    Route::post('logout', 'Auth\SessionController@logout');

    /*
     * These routes are prefixed with 'v1/bank-accounts'.
     */
    Route::prefix('bank-accounts')->group(function () {

        // Get user's all bank accounts.
        Route::get('', 'BankAccountController@index')->name('accounts.index');

        // Create a new bank account.
        Route::post('', 'BankAccountController@store')->name('accounts.store');

        // Get single bank account of user.
        Route::get('/{bankAccount}', 'BankAccountController@show')->name('accounts.show');

        // Update an existing bank account.
        Route::put('/{bankAccount}', 'BankAccountController@update')->name('accounts.update')->middleware('can:update,bankAccount');

        // Delete bank account.
        Route::delete('/{bankAccount}', 'BankAccountController@destroy')->name('accounts.destroy')->middleware('can:delete,bankAccount');
    });

});
