<?php

/*
|--------------------------------------------------------------------------
| B2B Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Admin

Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    
    Route::resource('wholesale-products', 'WholesaleProductController');
    Route::get('wholesale-products/{id}/edit', 'WholesaleProductController@edit')->name('wholesale-products.edit');
    Route::post('/wholesale-products/update/{id}', 'WholesaleProductController@update')->name('wholesale-products.update');
    Route::get('/wholesale-products/destroy/{id}', 'WholesaleProductController@destroy')->name('wholesale-products.destroy');
});
