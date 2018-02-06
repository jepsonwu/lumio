<?php
namespace Jiuyan\Request\Tool\Impl;

/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/8/8
 * Time: 上午10:21
 */
use Exception;
use Jiuyan\Request\Tool\SignInterface;

class Sign implements SignInterface
{
    protected $signLength = 45;
    protected $lastError;

    /**
     * @return mixed
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @param mixed $lastError
     */
    public function setLastError($lastError)
    {
        $this->lastError = $lastError;
    }
    /**
     * @return int
     */
    public function getSignLength()
    {
        return $this->signLength;
    }

    /**
     * @param int $signLength
     */
    public function setSignLength($signLength)
    {
        $this->signLength = $signLength;
    }


    public function makeSign($params, $expire, $version = 1)
    {
        // TODO: Implement makeSign() method.
    }

    public function generateStringForSign($param, $encode = true)
    {
        $param_filter = array();
        while (list ($key, $val) = each($param)) {
            if ($key == 'sign' || $val === '') {
                continue;
            } else {
                $param_filter[$key] = $param[$key];
            }
        }
        ksort($param_filter);//排序
        reset($param_filter);
        $arg = "";
        if ($encode) {
            while (list ($key, $val) = each($param_filter)) {
                $arg .= $key . "=" . rawurlencode($val) . "&";
                if (is_array($val)) {
                    throw new Exception(' val is not str! ' . ' key: ' . $key);
                }
            }
        } else {
            while (list ($key, $val) = each($param_filter)) {
                $arg .= $key . "=" . $val . "&";
            }
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }

    public function checkSign($str, $sign, $expire, $version = 1)
    {

        if (strlen($sign) !== $this->getSignLength()) {
            $this->setLastError(Sign::ERROR_LENGTH);
        }
        if (function_exists('jysign_check') && jysign_check($str, $sign, 1800)) {
            return true;
        }
        $this->setLastError(Sign::ERROR_SIGH);
        return false;
    }
}
