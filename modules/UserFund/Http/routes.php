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

    Route::get('/system-list', 'AccountController@systemList');
});

Route::version('v1', [
    'prefix' => '/api/user-fund/wallet',
    'namespace' => 'Modules\UserFund\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/', 'WalletController@detail');
    Route::get('/fund-record', 'WalletController@fundRecordList');

    Route::get('/recharge', 'WalletController@rechargeList');
    Route::post('/recharge', 'WalletController@recharge');
    Route::delete('/recharge/{id}', 'WalletController@closeRecharge');

    Route::get('/withdraw', 'WalletController@withdrawList');
    Route::post('/withdraw', 'WalletController@withdraw');
    Route::delete('/withdraw/{id}', 'WalletController@closeWithdraw');
});

