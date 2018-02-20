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

