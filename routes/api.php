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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

/**
 * All API endpoints are grouped under the 'api/v<version number>'
 * prefix and protected by the token authentication driver.
 */
Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    Route::resource('chats', 'ChatController');
    Route::resource('users', 'UserController');
});
