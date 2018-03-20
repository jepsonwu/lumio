<?php
/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/8/15
 * Time: 上午10:18
 */

return [
    'callback' => env("APP_DOMAIN") . '/admin',
    'router_prefix' => '/admin',
    'is_mock' => env('API_AUTH_IS_MOCK', false),
    'mock_user' => [
        'id' => 1
    ],
];