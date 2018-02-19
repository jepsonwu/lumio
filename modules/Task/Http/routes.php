<?php

Route::version('v1', ['prefix' => '/api/task', 'namespace' => 'Modules\Task\Http\Controllers'], function() {
    Route::get('/', 'ResourcesController@ping');
});

