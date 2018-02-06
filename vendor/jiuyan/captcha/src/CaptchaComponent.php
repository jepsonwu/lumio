<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/9
 * Time: 16:56
 */

namespace Jiuyan\Captcha;

class CaptchaComponent
{
    protected static $_selfInstance = null;

    /**
     * @return CaptchaManageComponent
     */
    public static function getInstance()
    {
        if (!self::$_selfInstance) {
            self::$_selfInstance = app('CaptchaManageComponent');
        }
        return self::$_selfInstance;
    }
}
