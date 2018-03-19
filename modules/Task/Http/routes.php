<?php

Route::version('v1', [
    'prefix' => '/api/task',
    'namespace' => 'Modules\Task\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/my', 'TaskController@myList');
    Route::get('/', 'TaskController@list');
    Route::get('/check', 'TaskController@isAllowCreate');
    Route::post('/', 'TaskController@create');
    Route::put('/{id}', 'TaskController@update');
    Route::delete('/{id}', 'TaskController@close');
});

Route::version('v1', [
    'prefix' => '/api/task-order',
    'namespace' => 'Modules\Task\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/', 'TaskOrderController@list');
    Route::get('/check-permission', 'TaskOrderController@checkPermission');
    Route::post('/', 'TaskOrderController@apply');
    Route::post('/assign/', 'TaskOrderController@assign');
    Route::post('/confirm/{id}', 'TaskOrderController@confirm');
    Route::put('/{id}', 'TaskOrderController@doing');
    Route::put('/done/{id}', 'TaskOrderController@done');
    Route::put('/freeze/{id}', 'TaskOrderController@freeze');
    Route::put('/verify/{id}', 'TaskOrderController@verify');
    Route::put('/seller-confirm/{id}', 'TaskOrderController@sellerConfirm');
    Route::put('/buyer-confirm/{id}', 'TaskOrderController@buyerConfirm');
    Route::delete('/{id}', 'TaskOrderController@close');
});

