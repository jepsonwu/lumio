<?php
/**
 * Created by IntelliJ IDEA.
 * User: topone4tvs
 * Date: 2017/10/09
 * Time: 14:13
 */

namespace Jiuyan\Captcha\Components;

use Jiuyan\Captcha\SendCaptchaBaseComponent;
use Jiuyan\Captcha\SendCaptchaContract;
use In\Sms\Sms;
use Log;
use Mockery\Exception;

class SmsCaptchaComponent extends SendCaptchaBaseComponent implements SendCaptchaContract
{
    const SMS_SEND_FLAG = 'in';

    public $captchaType = 'sms';

    protected function _send($target, $extFlag = '')
    {
        if (!Sms::checkMobile($target, self::SMS_SEND_FLAG)) {
            Log::error('captcha send target invalid');
            return false;
        }
        try {
            $sendRet = Sms::sendSms($target, 'in', [], self::SMS_SEND_FLAG);
        } catch (Exception $e) {
            return false;
        }
        if (!$sendRet || !isset($sendRet['success']) || !$sendRet['success']) {
            Log::error('captcha send failed err:' . json_encode($sendRet));
            return false;
        }
        Log::info('sms captcha send tg:' . $target);
        return $sendRet['code'];
    }
}