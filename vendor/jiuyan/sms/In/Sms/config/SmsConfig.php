<?php
/**
 * Created by PhpStorm.
 * User: zilaing
 * Date: 16/5/5
 * Time: 下午1:52
 */

/*
 * 返回配置项
 */

namespace In\Sms\config;

use In\Sms\Sms;

class SmsConfig
{


    static function loadConfig($type)
    {
        $content = include __DIR__ . "/" . $type . ".php";
        if ($content) {
            self::$config = array_merge(self::$config, $content);
        }

        if (Sms::$soaConfigGlobal) {
            self::$config['memcached'] = Sms::$soaConfigGlobal['memcached']['sms'];
            self::$config['mq']['sysqueue'] = Sms::$soaConfigGlobal['rabbitmq']['sms'];
            self::$config['mq']['send_sms_log'] = Sms::$soaConfigGlobal['rabbitmq']['sms'];
        }
    }

    static $config = array(

        //指定发短信的模板
        "smstemplate" => array(
            'in' => '手机验证码：code，请勿将验证码告知他人。【艾由】',
        )

    );

    const ERR_USER_PHONE_FORMAT_ERROR = 20107; // 手机号格式不对
    const ERR_USER_SMSCODE_SEND_FAILED = 20113; // 短信验证码发送失败
    const ERR_USER_SMS_CODE_ALREADY_SENT = 20114; // 短信验证码已经发送

}