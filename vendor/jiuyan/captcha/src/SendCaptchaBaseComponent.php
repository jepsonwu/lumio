<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/13
 * Time: 14:54
 */

namespace Jiuyan\Captcha;

use Log;

class SendCaptchaBaseComponent
{
    public $captchaType;

    protected function _send($target, $extFlag = '')
    {
        return '';
    }

    public function sendCaptcha($targetFlag, $cateFlag = '')
    {
        $errorBaseMsg = '';
        try {
            $captchaCode = '';
            if ($captchaCode = $this->_send($targetFlag, $cateFlag)) {
                return $captchaCode;
            }
            $errorBaseMsg = " tg:{$targetFlag} code:{$captchaCode} cate:{$cateFlag} type:{$this->captchaType}";
            Log::error('captcha send failed' . $errorBaseMsg);
        } catch (\Exception $e) {
            Log::error('captcha send error err:' . $e->getMessage() . $errorBaseMsg);
        } finally {
            //Log::error('captcha send got fetal error ' . $errorBaseMsg);
        }
        return false;
    }

    public function generateCaptcha($targetFlag, $cateFlag = '')
    {
        return mt_rand(1074, 9915);
    }
}