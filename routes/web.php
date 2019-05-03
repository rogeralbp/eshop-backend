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


/*
declaracion del controlador restfull
Route::resource('productos', 'ProductoController');

rutas basicas
Route::get('/productos','ProductoController@index');
Route::get('/producto/{nombre}','ProductoController@insert');
Route::get('/ejemplo/{nombre?}', function ($nombre='Fulanito') {
    return ('Hola ' . $nombre.', realizaste la primer ruta bien');
});
*/
//Auth::routes();
//Home
/*

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/estadistics', 'HomeController@estadistics');
Route::get('/cart', 'HomeController@cart');
Route::get('/explore/{idC}', 'HomeController@explore');
Route::get('/details/{idProducto}', 'HomeController@details');
Route::get('/history', 'HomeController@history');
Route::get('/records', 'HomeController@recordSales');
Route::get('/generar' , 'HomeController@generar');

//Categories
Route::get('/categories/create', 'CategoriaController@create');
Route::get('/categories/edit/{id}', 'CategoriaController@edit');
Route::get('/categories/index', 'CategoriaController@index');
Route::post('/categories/store', 'CategoriaController@store');
Route::get('/categories/destroy/{id}', 'CategoriaController@destroy');
Route::post('/categories/update/{id}', 'CategoriaController@update');
Route::resource('/categories', 'CategoriaController');

//Products
Route::get('/products/index', 'ProductoController@index');
Route::get('/products/create', 'ProductoController@create');
Route::get('/products/edit/{id}', 'ProductoController@edit');
Route::get('/products/detailProduct/{id}', 'ProductoController@detailProduct');
Route::get('/products/index', 'ProductoController@index');
Route::post('/products/store', 'ProductoController@store');
Route::get('/products/destroy/{id}', 'ProductoController@destroy');
Route::post('/products/update/{id}', 'ProductoController@update');
Route::resource('/products', 'ProductoController');

//Cart
Route::post('/cart/store/{idProducto}', 'CarritoController@store');
Route::get('/cart/destroy/{id}', 'CarritoController@destroy');

//Sales
Route::post('/buy/store', 'CompraController@store');
Route::post('/buy/paypal', 'CompraController@paypal');

//PDF
Route::get('pdf', 'ReportController@invoice');


//payment form

// route for processing payment
Route::post('paypal', 'PaymentController@payWithpaypal');

// route for check status of the payment
Route::get('status', 'PaymentController@getPaymentStatus');
*/
