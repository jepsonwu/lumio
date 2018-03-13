<?php

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers',
    'prefix' => '/admin',
], function (\Laravel\Lumen\Application $app) {
    $app->get('', 'HomeController@index');

    resource($app, '/demo', 'DemoController');
});

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers\Seller',
    'prefix' => '/admin/seller',
], function (\Laravel\Lumen\Application $app) {
    resource($app, '/store', 'StoreController');
});

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers\Account',
    'prefix' => '/admin/account',
], function (\Laravel\Lumen\Application $app) {
    resource($app, '/user', 'UserController');
});

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers\UserFund',
    'prefix' => '/admin/user-fund',
], function (\Laravel\Lumen\Application $app) {
    resource($app, '/withdraw', 'WithdrawController');
    resource($app, '/recharge', 'RechargeController');
});

