<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/9
 * Time: 16:39
 */
namespace Jiuyan\Captcha;

interface SendCaptchaContract
{
    public function generateCaptcha($targetFlag, $cateFlag = '');

    public function sendCaptcha($targetFlag, $cateFlag = '');
}
