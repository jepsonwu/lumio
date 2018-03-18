<?php

Route::version('v1', [
    'prefix' => '/api/seller/store',
    'namespace' => 'Modules\Seller\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/', 'StoreController@list');
    Route::post('/', 'StoreController@create');
    Route::get('/{id}', 'StoreController@detail');
    Route::put('/{id}', 'StoreController@update');
    Route::delete('/{id}', 'StoreController@delete');
});

Route::version('v1', [
    'prefix' => '/api/seller/goods',
    'namespace' => 'Modules\Seller\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/', 'GoodsController@list');
    Route::post('/', 'GoodsController@create');
    Route::get('/{id}', 'GoodsController@detail');
    Route::put('/{id}', 'GoodsController@update');
    Route::delete('/{id}', 'GoodsController@delete');
});

