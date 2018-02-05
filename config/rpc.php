<?php
return array(
    'user_center' => array(
        'client' => 'Jthrift\Services\UserInfoServiceClient',
        'hosts' => [ '10.10.106.28'],
        'port' => 9082,
        'persist' => false,
        'receive_timeout' => 2000,
        'send_timeout' => 1000,
        'read_buf_size' => 1024,
        'write_buf_size' => 1024,
        'host_picker' => null,
        'transport' => 'TBufferedTransport'
    ),
    'in_thrift' => array(
        'client' => 'In\ApiServiceClient',
        'hosts' => [ '10.10.108.96'],
        'port' => 9511,
        'persist' => false,
        'receive_timeout' => 2000,
        'send_timeout' => 1000,
        'read_buf_size' => 1024,
        'write_buf_size' => 1024,
        'host_picker' => null,
        'transport' => 'TBufferedTransport'
    ),
    'photo-service' => array(
        'client' => 'Jthrift\Services\UserInfoServiceClient',
        'hosts' => [ '10.10.106.28'],
        'port' => 9097,
        'persist' => false,
        'receive_timeout' => 2000,
        'send_timeout' => 1000,
        'read_buf_size' => 1024,
        'write_buf_size' => 1024,
        'host_picker' => null,
        'transport' => 'TBufferedTransport'
    ),

    'photo' => [
        'internal' => true,
        'namespace' => 'Modules\Photo\Services',
        'alias' => 'InternalService'
    ],
);
