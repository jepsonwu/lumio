<?php

app()->get('', function () {
    return 'In Lumio~';
});

//'middleware' => 'jiuyan.api.sign'
Route::version('v1', [
    'prefix' => '/api/account',
    'namespace' => 'Modules\Account\Http\Controllers'
], function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('password/reset', 'AuthController@resetPassword');
    Route::get('sms-captcha', 'AuthController@getSmsCaptcha');
});

Route::version('v1', [
    'prefix' => '/api/account',
    'namespace' => 'Modules\Account\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::put('password', 'AuthController@changePassword');
    Route::post('logout', 'AuthController@logout');
});

Route::version('v1', [
    'prefix' => '/api/user',
    'namespace' => 'Modules\Account\Http\Controllers',
    'middleware' => ['jiuyan.api.auth']
], function () {
    Route::get('/', 'UserController@myDetail');
    Route::get('/{id}', 'UserController@userDetail');
    Route::put('/', 'UserController@update');
});
