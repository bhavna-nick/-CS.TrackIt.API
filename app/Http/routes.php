<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
/*
Route::get('/', function () {
    return view('welcome');
}); */


Route::get('v1/users/history-log', 'UsersController@historyLogAllusers');

Route::post('v1/user/authenticate', 'UsersController@authenticate');
Route::post('v1/users', 'UsersController@usersCreate');
Route::get('v1/users', 'UsersController@getUsers');
Route::get('v1/users/{id}', 'UsersController@getUsersDetail');

//Route::put('v1/users/{id}', 'UsersController@editUser');

Route::delete('v1/users/{id}', 'UsersController@deleteUser');
Route::post('v1/users/forgotpassword', 'UsersController@forgotPassword');
Route::post('v1/users/reset-password/{id}', 'UsersController@resetPassword');
Route::post('v1/users/change-password','UsersController@changePassword');
Route::post('v1/users/change-password1','UsersController@changePassword1');

Route::post('v1/users/{id}', 'UsersController@editUser1');
Route::get('v1/users/history-log/{id}', 'UsersController@historyLog');



