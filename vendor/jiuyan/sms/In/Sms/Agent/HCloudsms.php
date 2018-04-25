<?php
/**
 * Created by PhpStorm.
 * User: World
 * Date: 16/12/7
 * Time: 下午2:37
 */

namespace In\Sms\Agent;

use In\Sms\config\SmsConfig;

class HCloudsms extends BaseSmsAgent
{

    protected $url = "http://sms.haotingyun.com/v2/sms/single_send.json";
    protected $user = "";
    protected $pwd = "";
    protected $api = "";

    public function send($type, $mobile, $content)
    {

        $data = ['apikey' => $this->api, 'mobile' => $mobile, 'text' => $content];

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HTTPHEADER,array('Accept:application/json','charset=utf-8','Content-Type:application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $ret = curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            throw new \Exception("An Error Occured");
        }
        $retArr = json_decode($ret, true);
        if ($retArr['code'] == 0) {
            return true;
        } else {
            return $retArr;
        }

    }


    public function getText($textGp, $textArg = array())
    {
//        $text = array(
//            'in'=>'手机验证码：code，请勿将验证码告知他人。【分享时尚-in】'
//        );
        $text = SmsConfig::$config['smstemplate'];
        $returnText = '';
        if (isset($text[$textGp])) {
            $returnText = str_replace(array_keys($textArg), array_values($textArg), $text[$textGp]);
        }
        return $returnText;
    }


}