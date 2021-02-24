<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/auth/send/reset/password','AuthController@send_reset_pass');

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signUp');
    Route::post('reset/password','AuthController@code_pass');
    Route::post('change/password','AuthController@change_password');
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::resource('products','ProductController');
        Route::resource('mesas','MesaController');
        Route::resource('events','EventController');
        Route::resource('tickets','TicketController');
        Route::resource('users', 'UserController');
        Route::resource('expenses', 'ExpenseController');
        Route::get('types','ProductController@get_type_produts');
        Route::get('products/type/{type}', 'ProductController@get_products_by_type');
        Route::get('mesas_active','NotesController@get_active');
        Route::get('get/available/info','NotesController@get_available_info');
        Route::post('add/active','NotesController@add_active');
        Route::delete('delete/active/{id}','NotesController@delete_active');
        Route::put('mesa/update_product/{id}','NotesController@update_product');
        Route::put('mesa/delete_product/{id}','NotesController@delete_product');
        Route::put('save/ticket/{id}','NotesController@save_ticket');
        Route::get('day/sales','TicketController@sale_day');
    });
});
