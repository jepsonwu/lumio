<?php
/**
 * Created by PhpStorm.
 * User: ziliang
 * Date: 16/5/5
 * Time: 上午11:46
 */
namespace In\Sms\log;
interface SmsLogInterface{

    /*
     * 短信发送情况日志
     */
    static function addSmsCodeInfoToQ($item);

    /*
     *验证码校验日志
     */
    static function addVerifyCodeInfoToQ($item);

    /*
     * 发送调试日志
     */
    static function addSmsDebugLog($item);


}