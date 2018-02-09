<?php

app()->group([
    'namespace' => 'Modules\Admin\Http\Controllers',
    'prefix' => '/admin',
], function (\Laravel\Lumen\Application $app) {
    $app->get('', 'HomeController@index');
});

