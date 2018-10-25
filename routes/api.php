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

Route::post('login', 'Api\AuthController@login')->name('api.login');

Route::middleware('auth:api')->name('api.')->group(function(){
	Route::post('logout', 'Api\AuthController@logout')->name('logout');
	
	//user
	Route::resource('user', 'Api\UserController')->except(['create', 'edit']);
	Route::patch('user/{user}/change-role', 'Api\UserController@changeRole')->name('user.changeRole');
	Route::patch('user/{user}/change-password', 'Api\UserController@changePassword')->name('user.changePassword');
	
	//customer
	Route::resource('customer', 'Api\CustomerController')->except(['create', 'edit']);
	
	//table
	Route::resource('table', 'Api\TableController')->except(['create', 'edit']);
	
	//Dish Category
	Route::resource('dish-category', 'Api\DishCategoryController')->except(['create', 'edit']);
	
	//Dish
	Route::resource('dish', 'Api\DishController')->except(['create', 'edit']);
	
	//Cart
	Route::resource('cart', 'Api\CartController')->except(['create', 'show', 'edit']);
	
	//order
	Route::resource('order', 'Api\OrderController')->except(['create', 'edit']);
	Route::patch('order/{order}/accept', 'Api\OrderController@accept')->name('order.accept');
	Route::patch('order/{order}/reject', 'Api\OrderController@reject')->name('order.reject');
	Route::patch('order/{order}/cook', 'Api\OrderController@cook')->name('order.cook');
	Route::patch('order/{order}/set-cooked', 'Api\OrderController@setCooked')->name('order.cooked');
	Route::patch('order/{order}/set-finished', 'Api\OrderController@setFinished')->name('order.finished');
	Route::post('order/{order}/pay', 'Api\OrderController@pay')->name('order.pay');
	
	//order detail
	Route::resource('order-detail', 'Api\OrderDetailController')->only(['update', 'destroy']);
	Route::post('order-detail/{order}/store', 'Api\OrderDetailController@store')->name('order-detail.store');
	
	//payment
	Route::resource('payment', 'Api\PaymentController')->only(['show', 'update', 'destroy']);
});
