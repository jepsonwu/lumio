<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/10
 * Time: 10:48
 */

namespace Jiuyan\Captcha\Components;

use GuzzleHttp\Client;
use Jiuyan\Captcha\CaptchaComponent;
use Jiuyan\Captcha\SendCaptchaBaseComponent;
use Jiuyan\Captcha\SendCaptchaContract;
use Log;

class VoiceCaptchaComponent extends SendCaptchaBaseComponent implements SendCaptchaContract
{
    public $captchaType = 'voice';

    protected function _send($target, $extFlag = '')
    {
        $appId = 'aaf98f89486445e601487c625f9f0797';
        $appKey = 'aaf98f8948406f53014858610fa20780';
        $appSecret = 'aaf98f8948406f53014858610fa2078037e4703bd29e4537b12c7c2ad0ccbf42';

        $httpClient = new Client();
        $captchaCode = $this->generateCaptcha($target, $extFlag);
        $requestData = json_encode([
            'appId' => $appId,
            'to' => $target,
            'verifyCode' => $captchaCode,
            'playTimes' => time()
        ]);
        $requestUrl = 'https://app.cloopen.com:8883/2013-12-26/Accounts/' . $appKey . '/Calls/VoiceVerify?sig=' . strtoupper(md5($appSecret . date('YmdHis')));
        $response = $httpClient->request(
            'POST',
            $requestUrl,
            [
                'Accept:application/json',
                'Content-Type: application/json;charset=utf-8',
                'Content-Length: ' . strlen($requestData),
                'Authorization:' . base64_encode($appKey . ':' . date('YmdHis'))
            ]
        );
        if ($response->getStatusCode() == '200') {
            return $captchaCode;
        }
        Log::info('captcha send tg:' . $target . ' code:' . $captchaCode . ' failed');
        return false;
    }
}