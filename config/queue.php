<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | The Laravel queue API supports a variety of back-ends via an unified
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "null", "sync", "database", "beanstalkd", "sqs", "redis"
    |
    */

    'default' => 'rabbitmq',

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 60,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 60,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => 'your-public-key',
            'secret' => 'your-secret-key',
            'queue' => 'your-queue-url',
            'region' => 'us-east-1',
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'retry_after' => 60,
        ],
        'kafka' => [
            'driver' => 'kafka',
            'groupid' => 'group_printer_center',
            'brokers' => '10.10.106.28:9992',
//            'brokers' => '10.10.106.216:9092,10.10.106.252:9092,10.10.106.251:9092,10.10.106.201:9092,10.10.106.225:9092,10.10.106.224:9092,10.10.106.222:9092',
            'queue' => 'order_process',
            'leader' => 1,
            'retries'=>3,
            'producer' => [
                'socket_timeout' => 5000,
                'send_tries' => 3,
                'request_timeout' => 5000,
                'wait_timeout' => 5000
            ],
            'consumer' => [
                'socket_timeout' => 5000,
                'enable_auto_offset_store' => true,
                'enable_auto_commit' => true,
                'auto_offset_reset' => 'beginning',
                'auto_commit_interval_ms' => 1000,
                'offset_store_method' => 'broker',
                'consume_time_out' => 5000
            ],
        ],
        'rabbitmq' => [
            'driver' => 'rabbitmq',

            'host' => env('RABBITMQ_HOST', '127.0.0.1'),
            'port' => env('RABBITMQ_PORT', 5672),

            'vhost'    => env('RABBITMQ_VHOST', '/'),
            'login'    => env('RABBITMQ_LOGIN', 'guest'),
            'password' => env('RABBITMQ_PASSWORD', 'guest'),

            'queue' => env('RABBITMQ_QUEUE', 'in_men'),
            // name of the default queue,

            'exchange_declare' => env('RABBITMQ_EXCHANGE_DECLARE', true),
            // create the exchange if not exists
            'queue_declare_bind' => env('RABBITMQ_QUEUE_DECLARE_BIND', true),
            // create the queue if not exists and bind to the exchange

            'queue_params' => [
                'passive'     => env('RABBITMQ_QUEUE_PASSIVE', false),
                'durable'     => env('RABBITMQ_QUEUE_DURABLE', true),
                'exclusive'   => env('RABBITMQ_QUEUE_EXCLUSIVE', false),
                'auto_delete' => env('RABBITMQ_QUEUE_AUTODELETE', false),
            ],
            'exchange_params' => [
                'name' => env('RABBITMQ_EXCHANGE_NAME', null),
                'type' => env('RABBITMQ_EXCHANGE_TYPE', 'direct'),
                // more info at http://www.rabbitmq.com/tutorials/amqp-concepts.html
                'passive' => env('RABBITMQ_EXCHANGE_PASSIVE', false),
                'durable' => env('RABBITMQ_EXCHANGE_DURABLE', true),
                // the exchange will survive server restarts
                'auto_delete' => env('RABBITMQ_EXCHANGE_AUTODELETE', false),
            ],

            // the number of seconds to sleep if there's an error communicating with rabbitmq
            // if set to false, it'll throw an exception rather than doing the sleep for X seconds
            'sleep_on_error' => env('RABBITMQ_ERROR_SLEEP', 5),
        ],
        'rabbitMq1' => [
            'driver' => 'rabbitmq',
            'host' => env('RABBIT_MQ_HOST_1', '127.0.0.1'),
            'port' => env('RABBIT_MQ_PORT_1', 5672),
            'vhost'    => env('RABBIT_MQ_VHOST_1', '/'),
            'login'    => env('RABBIT_MQ_LOGIN_1', 'guest'),
            'password' => env('RABBIT_MQ_PASSWORD_1', 'guest'),
            'queue' => env('RABBIT_MQ_QUEUE_1', 'in_men'),
            'exchange_declare' => env('RABBIT_MQ_EXCHANGE_DECLARE_1', true),
            'queue_declare_bind' => env('RABBIT_MQ_QUEUE_DECLARE_BIND_1', true),
            'queue_params' => [
                'passive'     => env('RABBIT_MQ_QUEUE_PASSIVE_1', false),
                'durable'     => env('RABBIT_MQ_QUEUE_DURABLE_1', true),
                'exclusive'   => env('RABBIT_MQ_QUEUE_EXCLUSIVE_1', false),
                'auto_delete' => env('RABBIT_MQ_QUEUE_AUTODELETE_1', false),
            ],
            'exchange_params' => [
                'name' => env('RABBIT_MQ_EXCHANGE_NAME_1', null),
                'type' => env('RABBIT_MQ_EXCHANGE_TYPE_1', 'direct'),
                'passive' => env('RABBIT_MQ_EXCHANGE_PASSIVE_1', false),
                'durable' => env('RABBIT_MQ_EXCHANGE_DURABLE_1', true),
                'auto_delete' => env('RABBIT_MQ_EXCHANGE_AUTODELETE_1', false),
            ],
            'sleep_on_error' => env('RABBIT_MQ_ERROR_SLEEP_1', 5),
        ]

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => env('QUEUE_FAILED_TABLE', 'failed_jobs'),
    ],

];
