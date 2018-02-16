<?php

Route::version('v1', [
    'prefix' => '/api/user-fund',
    'namespace' => 'Modules\UserFund\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/', 'AccountController@list');
    Route::post('/', 'AccountController@create');
    Route::put('/{id}', 'AccountController@update');
    Route::delete('/{id}', 'AccountController@delete');
});

