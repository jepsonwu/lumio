<?php
/**
 * thrift  配置wiki：http://wiki.in66.cc/pages/viewpage.action?pageId=4403496
 */

return [
    'userCenter' => [
        'client' => 'UserInfoServiceClient',
        'port' => env('THRIFT_CLIENTS_USER_CENTER_PORT_1'),
        'hosts' => [
            env('THRIFT_CLIENTS_USER_CENTER_HOST_1'),
            env('THRIFT_CLIENTS_USER_CENTER_HOST_2'),
            env('THRIFT_CLIENTS_USER_CENTER_HOST_3'),
        ]
    ],
    'inServerCenter' => [
        'client' => 'ApiServiceClient',
        'port' => env('IN_SERVICE_THRIFT_SERVICE_PORT_1', 9611),
        'hosts' => [
            env('IN_SERVICE_THRIFT_SERVICE_HOST_1'),
            env('IN_SERVICE_THRIFT_SERVICE_HOST_2'),
            env('IN_SERVICE_THRIFT_SERVICE_HOST_3'),
            env('IN_SERVICE_THRIFT_SERVICE_HOST_4'),
            env('IN_SERVICE_THRIFT_SERVICE_HOST_5'),
        ],
    ],
    'openCenter' => [
        'client' => 'OpenplatformServiceClient',
        'port' => env('THRIFT_CLIENTS_OPEN_CENTER_PORT_1', 9082),
        'hosts' => [
            env('THRIFT_CLIENTS_OPEN_CENTER_HOST_1'),
            env('THRIFT_CLIENTS_OPEN_CENTER_HOST_2'),
        ],
    ],
];