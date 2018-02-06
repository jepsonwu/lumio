<?php
/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/8/8
 * Time: 上午11:08
 */

namespace Jiuyan\Request\Tool\Impl;

use Jiuyan\Request\Tool\SignInterface;

class SignMD5 extends Sign implements SignInterface
{

    public function makeSign($params, $expire, $version = 1)
    {
        return $version.'.'.md5($params).'.'.$expire;
    }
    public function checkSign($str, $sign, $expire, $version = 1)
    {

        if (strlen($sign) !== $this->getSignLength()) {
            $this->setLastError(Sign::ERROR_LENGTH);
        }
        $makeSign = $this->makeSign($str, $expire, $version);
        if ($makeSign == $sign) {
            return true;
        }
        $this->setLastError(Sign::ERROR_SIGH);
        return false;
    }
}
