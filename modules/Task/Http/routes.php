<?php

Route::version('v1', [
    'prefix' => '/api/task',
    'namespace' => 'Modules\Task\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
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
    Route::get('/check', 'TaskOrderController@isAllowApply');
    Route::post('/', 'TaskOrderController@apply');
    Route::post('/assign/', 'TaskOrderController@assign');
    Route::post('/confirm/{id}', 'TaskOrderController@confirm');
    Route::put('/{id}', 'TaskOrderController@doing');
    Route::put('/done/{id}', 'TaskOrderController@done');
    Route::delete('/{id}', 'TaskOrderController@close');
});

