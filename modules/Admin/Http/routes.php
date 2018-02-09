<?php

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers',
    'prefix' => '/admin',
], function (\Laravel\Lumen\Application $app) {
    $app->get('', 'HomeController@index');

    //list create edit delete todo package
    $app->get('/demo', 'DemoController@index');
    $app->get('/demo/create', 'DemoController@create');
    $app->get('/demo/{id}', 'DemoController@show');
    $app->get('/demo/edit/{id}', 'DemoController@edit');
    $app->post('/demo', 'DemoController@store');
    $app->delete('/demo/{id}', 'DemoController@destroy');
});

