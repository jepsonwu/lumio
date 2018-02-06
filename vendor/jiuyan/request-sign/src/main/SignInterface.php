<?php
/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/8/8
 * Time: 上午10:34
 */

namespace Jiuyan\Request\Tool;

interface SignInterface
{
    const ERROR_LENGTH = 10001;
    const ERROR_SIGH = 10002;
    public function generateStringForSign($params, $rawDecode);
    public function makeSign($params, $expire, $version);
    public function checkSign($str, $sign, $expire, $version);
    public function getLastError();

    public function setSignLength($signLength);
    public function getSignLength();
}
