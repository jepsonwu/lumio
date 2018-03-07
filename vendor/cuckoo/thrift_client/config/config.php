<?php
return array(
    'default' => array(),
    'local' => array(
        'client' => 'Banyandb\BanyandbServiceClient',
        'hosts' => ['127.0.0.1:8092,127.0.0.1:8092'],  //hosts 配置支持两种格式ip:port,ip:port/ip,ip
        //'hosts' => ['127.0.0.1]
        'port' => 8092,
        'persist' => false,
        'receive_timeout' => 2000,
        'send_timeout' => 1000,
        'read_buf_size' => 1024,
        'write_buf_size' => 1024,
        'host_picker' => null,
        'tracked' => true,
        'trace_header' => true,  // 链路追踪 是否开启header
        'binary' => '', // 默认选择TBinaryProtocolAccelerated， 配置jiuyan, 选择JiuyanTBinaryProtocol
        'downgrad_allowed' => false, // 是否允许服务降级, 如果是特别边缘的服务可以配置为true，服务挂了不影响主流程
        'service_name'=>'', //binary为Jiuyan时，同时配置service_name选项
        'stats' => array(
            'host' => '127.0.0.1',
            'port' => 8125,
            'timeout' => 1,
            'table' => 'in',
            'application' => '.apps.',     //固定名称 不用修改
            'department' => 'middle'  //部门
        )
    ));
