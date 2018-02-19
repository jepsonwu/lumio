<?php

Route::version('v1', [
    'prefix' => '/api/seller/store',
    'namespace' => 'Modules\Seller\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/', 'StoreController@list');
    Route::post('/', 'StoreController@create');
    Route::put('/{id}', 'StoreController@update');
    Route::delete('/{id}', 'StoreController@delete');
});

