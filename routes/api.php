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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
/*
Route::post('login', 'AuthController@login');
Route::post('singup', 'AuthController@singup');
Route::post('logout', 'AuthController@logout');
Route::post('refresh','AuthController@refresh');
*/
Route::resource('carts','CarritoController');

Route::resource('categories','CategoriaController');

Route::post('buy/home', 'CompraController@store');
Route::get('buy/paypal', 'CompraController@paypal');

Route::resource('home','HomeController');
Route::get('home/index/{id_usuario}', 'HomeController@index');
Route::get('home/estadistics{idUsuario}', 'HomeController@estadistics');
Route::get('home/cart{idUsuario}', 'HomeController@cart');
Route::get('home/explore/{idCategoria}', 'HomeController@explore');
Route::get('home/details/{idProducto}', 'HomeController@details');
Route::get('home/history{idUsuario}', 'HomeController@history');
Route::get('home/records{idUsuario}', 'HomeController@recordSales');
Route::get('home/generar' , 'HomeController@generar');

Route::resource('products','ProductoController');
Route::get('products/detailProduct/{idProducto}', 'ProductoController@detailProduct');


