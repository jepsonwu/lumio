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

/**
 * 邮件验证码发放模块
 * 默认账号密码：captcha@in66.com CAPTCHA@in66
 * Class MailCaptchaComponent
 * @package Jiuyan\Captcha\Components
 */
class MailCaptchaComponent extends SendCaptchaBaseComponent implements SendCaptchaContract
{
    public $captchaType = 'mail';

    protected function _send($target, $extFlag = '')
    {
        $mailAddressRule = '/^\w+(\.\w+)*@\w+(\.\w+)+$/';
        if (!preg_match($mailAddressRule, $target)) {
            Log::error('mail captcha send target invalid tg:' . $target);
            return false;
        }
        $captchaCode = $this->generateCaptcha($target, $extFlag);
        \Mail::send('emails.' . $extFlag . '_captcha', ['captcha' => $captchaCode], function ($msg) use ($target) {
            $msg->to($target)->subject('验证码');
        });
        Log::info('mail captcha send tg:' . $target . ' code:' . $captchaCode);
        return $captchaCode;
    }
}