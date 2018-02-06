<?php
/**
 * Created by PhpStorm.
 * User: ziliang
 * Date: 16/5/25
 * Time: 下午4:49
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
    "channel"=>array('Montnets','ZhuTong','XinYiChen','HCloud'),
    "channelRatio"=>array('Montnets'=>'40','ZhuTong'=>'20','XinYiChen'=>'30','HCloud'=>'10'),
    "channelAccount"=>array("Montnets"=>array('user'=>'send01','pwd'=>'123456','url'=>''),
        //"TuoPeng"=>array('user'=>'hzjy','pwd'=>'hzjy5858'),
        "XinYiChen"=>array('user'=>'admin','pwd'=>'4a3db8fdb340de54c7f7b1d6282a39cd','url'=>''),
        "ZhuTong"=>array('user'=>'jiuqikejiyzm','pwd'=>'kPLLe3','url'=>'http://www.yzmsms.cn/sendSmsYZM.do'),
        "HCloud"=>array('user'=>'18989863314','pwd'=>'abc123456','url'=>'http://sms.haotingyun.com/v2/sms/single_send.json'))

);