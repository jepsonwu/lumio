<?php

app()->get('', function () {
    return 'In Lumio~';
});

Route::version('v1', ['prefix' => '/api/account', 'namespace' => 'Modules\Account\Http\Controllers'], function () {//'middleware' => 'jiuyan.api.sign'
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('password/reset', 'AuthController@resetPassword');
    Route::get('sms-captcha', 'AuthController@getSmsCaptcha');
});

Route::version('v1', ['prefix' => '/api/account', 'namespace' => 'Modules\Account\Http\Controllers', 'middleware' => ['jiuyan.api.auth']], function () {
    Route::post('password', ['as' => 'set.password', 'uses' => 'AuthController@setPassword']);
    Route::put('password', ['as' => 'change.password', 'uses' => 'AuthController@changePassword']);
    Route::post('logout', 'AuthController@logout');
});
