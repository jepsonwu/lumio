<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/23
 * Time: 10:47
 */

namespace App\Components;

use Jiuyan\Captcha\CaptchaManageComponent;

class CustomizeCaptchaManageComponent extends CaptchaManageComponent
{
//    protected function _dealCaptcha($ope, $cateFlag, $mobile, $captcha = '')
//    {
//        $cacheKey = $mobile . ($cateFlag ? '_' . $cateFlag : '');
//        switch ($ope) {
//            case 'set':
//                /**
//                 * TODO:: 默认设置成功
//                 */
//                $this->_getCacheRepository()->set($cacheKey, $captcha . '_' . time());
//                return true;
//                break;
//            case 'get':
//                $captchaRes = $this->_getCacheRepository()->get($cacheKey);
//                return (strpos($captchaRes, '_') !== false) ? preg_replace('/_.*/', '', $captchaRes) : $captchaRes;
//                break;
//            default:
//                break;
//        }
//    }
}