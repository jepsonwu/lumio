<?php

Route::version('v1', [
    'prefix' => '/api/user-fund/account',
    'namespace' => 'Modules\UserFund\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/', 'AccountController@list');
    Route::post('/', 'AccountController@create');
    Route::put('/{id}', 'AccountController@update');
    Route::delete('/{id}', 'AccountController@delete');
});

Route::version('v1', [
    'prefix' => '/api/user-fund/wallet',
    'namespace' => 'Modules\UserFund\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/', 'WalletController@list');
    Route::post('/recharge', 'WalletController@recharge');
    Route::put('/recharge/{id}', 'WalletController@updateRecharge');
    Route::delete('/recharge/{id}', 'WalletController@deleteRecharge');
    Route::post('/withdraw', 'WalletController@withdraw');
    Route::delete('/withdraw/{id}', 'WalletController@deleteWithdraw');
});

