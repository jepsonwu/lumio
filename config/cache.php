<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */

    'default' => env('CACHE_DRIVER', 'memcached'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
        ],

        'database' => [
            'driver' => 'database',
            'table'  => env('CACHE_DATABASE_TABLE', 'cache'),
            'connection' => env('CACHE_DATABASE_CONNECTION', null),
        ],

        'file' => [
            'driver' => 'file',
            'path'   => storage_path('framework/cache'),
        ],

        'memcached' => [
            'driver'  => 'memcached',
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'), 'port' => env('MEMCACHED_PORT', 11211), 'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('CACHE_REDIS_CONNECTION', 'default'),
        ],

        'share-session' =>[
            'driver' => 'memcached',
            'servers' => [
                [
                    'host' => env('MEMCACHED_SESSION_HOST_1', '127.0.0.1'),
                    'port' => env('MEMCACHED_SESSION_PORT_1', 11211),
                    'weight' => 100,
                ],
                [
                    'host' => env('MEMCACHED_SESSION_HOST_2', '127.0.0.1'),
                    'port' => env('MEMCACHED_SESSION_PORT_2', 11211),
                    'weight' => 100,
                ],
                [
                    'host' => env('MEMCACHED_SESSION_HOST_3', '127.0.0.1'),
                    'port' => env('MEMCACHED_SESSION_PORT_3', 11211),
                    'weight' => 100,
                ],
            ],
            /**
             * 暂时不区分环境，直接写死了
             */
            'prefix' => 'd1_production-wx_v_1.0_session'
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing a RAM based store such as APC or Memcached, there might
    | be other applications utilizing the same cache. So, we'll specify a
    | value to get prefixed to all our keys so we can avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', 'laravel'),

];
