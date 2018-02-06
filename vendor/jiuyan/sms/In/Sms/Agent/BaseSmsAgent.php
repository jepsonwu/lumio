<?php
/**
 * Created by PhpStorm.
 * User: World
 * Date: 16/10/31
 * Time: 下午4:16
 */

namespace In\Sms\Agent;


class BaseSmsAgent implements SmsAgentInterface
{
    protected $url = '';
    protected $user = '';
    protected $pwd = '';

    public function send($type, $mobile, $content)
    {
        // TODO: Implement send() method.
    }

    public function setAccount($user, $pwd)
    {
        // TODO: Implement setAccount() method.
        if ($user && $pwd) {
            $this->user = $user;
            $this->pwd = $pwd;
        }
    }

    public function setUrl($url)
    {
        // TODO: Implement setUrl() method.
        if(!empty($url))
            $this->url = $url;
    }


}