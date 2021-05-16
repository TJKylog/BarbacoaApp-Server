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
        Route::get('logout', 'AuthController@logout');//cerrar sesión
        Route::get('user', 'AuthController@user');//usuario logueado
        Route::resource('products','ProductController');//rutas de los productos
        Route::resource('mesas','MesaController');//rutas de las mesas
        Route::resource('events','EventController');//rutas de los eventos
        Route::resource('tickets','TicketController');//rutas de los tickets
        Route::resource('users', 'UserController');//rutas de los usuarios
        Route::resource('expenses', 'ExpenseController');//rutas de los egresos
        Route::get('types','ProductController@get_type_produts');//ruta de los tipos de productos
        Route::get('products/type/{type}', 'ProductController@get_products_by_type');//ruta que mustra los productos por tipo
        Route::get('mesas_active','NotesController@get_active');//ruta que muestra la mesas activas
        Route::get('get/available/info','NotesController@get_available_info');// ruta que muestra las mesas disponibles y meseros
        Route::post('add/active','NotesController@add_active');//ruta que añade una mesa activa
        Route::delete('delete/active/{id}','NotesController@delete_active');//ruta que borra unn mesa activa
        Route::put('mesa/update_product/{id}','NotesController@update_product');// añade o modifica la cantidad consumida de una mesa activa
        Route::put('mesa/delete_product/{id}','NotesController@delete_product');// borra un articulo del consumo de una mesa
        Route::put('save/ticket/{id}','NotesController@save_ticket');//ruta que guarda el ticket
        Route::get('day/sales','TicketController@sale_day');// ruta que obtiene la venta del día
        Route::post('verify/name', 'ProductController@validate_name');//ruta que verifica que el nombre del producto no existe
        Route::get('get_names', 'UserController@user_names');//ruta que obtiene el nombre de los usuarios
        Route::get('set/invoice/{id}','NotesController@set_invoice_note');//ruta que fija un folio a una mesa activa
    });
});
