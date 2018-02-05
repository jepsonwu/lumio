<?php

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'qa_in_notice_main_new'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', '123456'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        'tags' => [
            'read' => [
                'host' => ['10.10.106.218']
            ],
            'write' => [
                'host' => ['10.10.106.218']
            ],
            'driver' => 'mysql',
            'database' => 'in_men',
            'username' => 'proin',
            'password' => ':Fn+{+qgG+3u#Q8gZB*a',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],
        'photo_tag_map' => [
            'read' => [
                'host' => ['10.10.106.218']
            ],
            'write' => [
                'host' => ['10.10.106.218']
            ],
            'driver' => 'mysql',
            'database' => 'in_men',
            'username' => 'proin',
            'password' => ':Fn+{+qgG+3u#Q8gZB*a',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],
        'photo' => [
            'read' => [
                'host' => ['10.10.106.218']
            ],
            'write' => [
                'host' => ['10.10.106.218']
            ],
            'driver' => 'mysql',
            'database' => 'in_men',
            'username' => 'proin',
            'password' => ':Fn+{+qgG+3u#Q8gZB*a',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],
        'comment' => [
            'read' => [
                'host' => ['10.10.106.218']
            ],
            'write' => [
                'host' => ['10.10.106.218']
            ],
            'driver' => 'mysql',
            'database' => 'in_men',
            'username' => 'proin',
            'password' => ':Fn+{+qgG+3u#Q8gZB*a',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],
        'collection' => [
            'read' => [
                'host' => ['10.10.106.218']
            ],
            'write' => [
                'host' => ['10.10.106.218']
            ],
            'driver' => 'mysql',
            'database' => 'in_men',
            'username' => 'proin',
            'password' => ':Fn+{+qgG+3u#Q8gZB*a',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],
        'notice' => [
            'driver' => 'mysql',
            'host' => '10.10.106.238',
            'port' => env('DB_PORT', '3306'),
            'database' => 'qa_in_notice_main_new',
            'username' => 'proin',
            'password' => ':Fn+{+qgG+3u#Q8gZB*a',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        'user_ext' => [
            'driver' => 'mysql',
            'host' => '10.10.106.218',
            'port' => env('DB_PORT', '3306'),
            'database' => 'in_user_ext',
            'username' => 'proin',
            'password' => ':Fn+{+qgG+3u#Q8gZB*a',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

    ],

    'migrations' => 'migrations',
];
