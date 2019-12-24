<?php

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

//This route will be called when the requested url not found 
Route::fallback(function () {
    return response()->json(['message' => 'Page Not Found'], 404);
});
//Create Order Route
Route::post('/orders', 'OrderController@createOrder');
//List Order Route
Route::get('/orders', 'OrderController@orderList');
//Update Order Route
Route::patch('/orders/{id}/', 'OrderController@updateOrderStatus');
