<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/listen', 'HomeController@listen');
Route::get('/start-conversation', 'HomeController@start');
Route::get('/chat/{conversationId}/{representativeId}', 'HomeController@chat');
Route::get('/conversations', 'HomeController@conversations');
Route::get('/conversation/{conversationId}', 'HomeController@conversation');
Route::get('/representatives', 'HomeController@representatives');
