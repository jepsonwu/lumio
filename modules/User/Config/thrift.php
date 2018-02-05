<?php
/**
 * thrift 配置
 * Author: flashytime
 * Date: 17/2/23 10:21
 * modify: shizhu 将in中的thrift整合到一起
 */

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
        'send_timeout' => 3000,
        'receive_timeout' => 3000,
        'port' => env('IN_SERVICE_THRIFT_SERVICE_PORT_1', 9611),
        'hosts' => [
            env('IN_SERVICE_THRIFT_SERVICE_HOST_1'),
            env('IN_SERVICE_THRIFT_SERVICE_HOST_2'),
            env('IN_SERVICE_THRIFT_SERVICE_HOST_3'),
            env('IN_SERVICE_THRIFT_SERVICE_HOST_4'),
            env('IN_SERVICE_THRIFT_SERVICE_HOST_5'),
        ],
        'persist' => false,
        'read_buf_size' => 1024,
        'write_buf_size' => 1024,
        'host_picker' => 'null',
        'transport' => 'TBufferedTransport',
        'tracked' => true,
        'trace_header' => true,  // 链路追踪 是否开启header
        'binary' => '', // 默认选择TBinaryProtocolAccelerated， 配置jiuyan, 选择JiuyanTBinaryProtocol
        'downgrad_allowed' => false, // 是否允许服务降级, 如果是特别边缘的服务可以配置为true，服务挂了不影响主流程
        'service_name' => '' //binary为Jiuyan时，同时配置service_name选项
    ],
];