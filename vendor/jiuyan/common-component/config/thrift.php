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
    'common' => [
        'client_namespace' => "Jthrift\\Services\\",
        'send_timeout' => 5000,
        'receive_timeout' => 5000,
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
    ]
];