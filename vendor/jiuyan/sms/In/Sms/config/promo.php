<?php
/**
 * Created by PhpStorm.
 * User: ziliang
 * Date: 16/7/14
 * Time: 下午3:16
 */

return array(
    //缓存memcached配置
//    "memcached" => array(array("host" => "localhost", "port" => "11211")),
    "memcached" => array(array('host' => 'memcache-host-1', 'port' => '12233'),
        array('host' => 'memcache-host-2', 'port' => '12233')),
    //消息队列配置: 队列名称:详细配置信息
    "mq" => array('sysqueue' => array('host' => '10.10.106.9', 'port' => '5672', 'user' => 'guest', 'pass' => 'guest',
        'vhost' => '/', 'debug' => false, 'connection_timeout' => 1, 'read_write_timeout' => 3),

        'send_sms_log' => array('host' => '10.10.106.9', 'port' => '5672', 'user' => 'guest', 'pass' => 'guest',
            'vhost' => '/', 'debug' => false, 'connection_timeout' => 1, 'read_write_timeout' => 3)),
    //短信发送渠道和权重设置
    "channel"=>array('Montnets','ZhuTong'),
    "channelRatio"=>array('Montnets'=>'55','ZhuTong'=>'45'),
    "channelAccount"=>array("Montnets"=>array('user'=>'send02','pwd'=>'123456','url'=>''),
        "ZhuTong"=>array('user'=>'jiuqikejiyx','pwd'=>'n4Rk56','productid'=>'160225','url'=>'http://www.api.zthysms.com/sendSms.do')
//                            "ZhuTong"=>array('user'=>'jiuqikejihy','pwd'=>'DlfA0g','productid'=>'160225','url'=>'http://www.zthysms.cn:8810/sendSms.do')
    )

);