<?php

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers',
    'prefix' => '/admin',
    'middleware' => ['jiuyan.admin.auth']
], function (\Laravel\Lumen\Application $app) {
    $app->get('', 'HomeController@index');

    //resource($app, '/demo', 'DemoController');
});

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers\Auth',
    'prefix' => '/admin/auth',
], function (\Laravel\Lumen\Application $app) {
    $app->get("/login", 'AccountController@index');
    $app->post("/login", 'AccountController@login');
    $app->get("/logout", 'AccountController@logout');
});

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers\Auth',
    'prefix' => '/admin/auth',
    'middleware' => ['jiuyan.admin.auth']
], function (\Laravel\Lumen\Application $app) {
    $app->post("/logout", 'AccountController@logout');
});

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers\Seller',
    'prefix' => '/admin/seller',
    'middleware' => ['jiuyan.admin.auth']
], function (\Laravel\Lumen\Application $app) {
    resource($app, '/store', 'StoreController');

    $app->post("/store/{id}/verify-fail", 'StoreController@verifyFail');
    $app->post("/store/{id}/verify-pass", 'StoreController@verifyPass');
});

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers\Account',
    'prefix' => '/admin/account',
    'middleware' => ['jiuyan.admin.auth']
], function (\Laravel\Lumen\Application $app) {
    resource($app, '/user', 'UserController');
});

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers\UserFund',
    'prefix' => '/admin/user-fund',
    'middleware' => ['jiuyan.admin.auth']
], function (\Laravel\Lumen\Application $app) {
    resource($app, '/withdraw', 'WithdrawController');
    $app->post("/withdraw/{id}/verify-fail", 'WithdrawController@verifyFail');
    $app->post("/withdraw/{id}/verify-pass", 'WithdrawController@verifyPass');

    resource($app, '/recharge', 'RechargeController');
    $app->post("/recharge/{id}/verify-fail", 'RechargeController@verifyFail');
    $app->post("/recharge/{id}/verify-pass", 'RechargeController@verifyPass');

    resource($app, '/record', 'RecordController');
    resource($app, '/account', 'AccountController');
    resource($app, '/', 'FundController');
});

