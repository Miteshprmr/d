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

/*
 * These routes are prefixed with 'v1'.
 * These routes use the root namespace 'App\Api\V1\Http\Controllers'.
 */
Route::prefix('v1')->namespace('V1\Http\Controllers')->group(function () {

    // Will automatically include all the route files from the "api/v1" folder.
    include_route_files('api/v1');

    // The catch all route.
    Route::get('{any?}', 'HomeController@catchAll')
        ->where('any', '.*')
        ->name('api-catch-all');
});