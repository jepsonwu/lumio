<?php
/**
 * Created by PhpStorm.
 * User: Ziliang
 * Date: 16/5/5
 * Time: 下午4:57
 */
namespace In\Sms\Agent;
interface SmsAgentInterface{
    /**
     * 发送短信
     * @param string $type 短信的类型
     * @param string $mobile 接收信息的手机号
     * @param string $content 发送内容
     */
    public function send($type, $mobile, $content);

    public function setAccount($user,$pwd);
    
    public function setUrl($url);
    
}