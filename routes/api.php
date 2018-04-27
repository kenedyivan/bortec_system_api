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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/operators/register', 'OperatorRegisterController@register');
Route::post('/operators/login', 'OperatorLoginController@login');

//Items routes
Route::post('/items', 'ItemsController@save');
Route::get('/items/show', 'ItemsController@show');

//Received products routes
Route::post('/items/received', 'ReceivedProductsController@save');

//Sales routes
Route::post('/items/sales', 'SalesController@save');
Route::get('/items/operator/sales', 'OperatorSalesController@getOperatorSalesLog');