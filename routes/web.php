<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// authentication routes
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');

Route::group(['middleware' => ['auth']], function(){
	Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
	Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
	Route::resource('order', 'OrderController')->only(['index', 'create', 'edit']);
});



