<?php
/**
 * Created by PhpStorm.
 * User: World
 * Date: 16/6/15
 * Time: 上午9:42
 */

namespace In\Sms\Agent;

use In\Sms\config\SmsConfig;

class XinYiChensms extends BaseSmsAgent
{

    /**
     * @var int
     */
    const INFINITE_TIMEOUT = 0;

    /**
     * 请求的超时时间, 单位为秒.
     *
     * @var int
     */
    protected $timeout = 3;

    /**
     * 连接的超时时间, 单位为秒.
     *
     * @var int
     */
    protected $connectTimeout = 3;

    protected $url = "http://113.108.68.228:8001/sendSMS.action";
    protected $user = "admin";
    protected $pwd = "4a3db8fdb340de54c7f7b1d6282a39cd";



    public function send($type, $mobile, $content)
    {
        $data = array(
            'enterpriseID' => '16787',
            'loginName' => $this->user,
            'password' => $this->pwd,
            'content' => $content,
            'mobiles' => $mobile,
        );
        return $this->request($this->url, $data);

    }

    private function request($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);

        // 请求超时需要特殊处理
        if ($this->timeout !== self::INFINITE_TIMEOUT) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        }

        curl_exec($ch);
        $res = true;
        if (curl_errno($ch))
            $res = false;

        curl_close($ch);
        return $res;

    }

    public function getText($textGp, $textArg = array())
    {
//        $text = array(
//            'in'=>'手机验证码：code，请勿将验证码告知他人【我的生活in记】',
//            'promo'=>'你的好友name在in里关注了你，马上去看看 url。退订回复TD【我的生活in记】',
//            'comment' => '你的好友name在in里面回复了你，去看看>url【我的生活in记】',
//            'watch' => '哇哦，name在in里面关注你了，快去in中看看ta是谁吧>url【我的生活in记】',
//        );
        $text = SmsConfig::$config['smstemplate'];
        $returnText = '';
        if (isset($text[$textGp])) {
            $returnText = str_replace(array_keys($textArg), array_values($textArg), $text[$textGp]);
        }
        return $returnText;
    }
    
}