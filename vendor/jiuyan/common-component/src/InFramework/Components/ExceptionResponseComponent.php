<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/3
 * Time: 14:52
 */

namespace Jiuyan\Common\Component\InFramework\Components;

use Jiuyan\Common\Component\InFramework\Exceptions\ApiExceptions;
use Jiuyan\Common\Component\InFramework\Exceptions\BusinessException;
use Jiuyan\Common\Component\InFramework\Exceptions\ServiceException;
use Jiuyan\Common\Component\InFramework\Exceptions\ThriftResponseException;
use Exception;

class ExceptionResponseComponent
{
    private static function _formatTpl(&$errorTpl)
    {
        $errorInfo = explode('|', $errorTpl);
        $errCode = $errorInfo[0] ?? 100001;
        $errMsg = $errorInfo[1] ?? 'system error';
        return [$errMsg, $errCode];
    }

    public static function convert(Exception $exception)
    {
        $errMsg = $exception->getMessage();
        $errCode = $exception->getCode();
        return $errCode . '|' . $errMsg;
    }

    public static function api($errorTpl)
    {
        list($errMsg, $errCode) = self::_formatTpl($errorTpl);
        throw new ApiExceptions($errMsg, $errCode);
    }

    public static function business($errorTpl)
    {
        list($errMsg, $errCode) = self::_formatTpl($errorTpl);
        throw new BusinessException($errMsg, $errCode);
    }

    public static function service($errorTpl)
    {
        list($errMsg, $errCode) = self::_formatTpl($errorTpl);
        throw new ServiceException($errMsg, $errCode);
    }

    public static function thrift($errorTpl)
    {
        list($errMsg, $errCode) = self::_formatTpl($errorTpl);
        throw new ThriftResponseException($errMsg, $errCode);
    }

    public static function customize($errorTpl, $customizeException)
    {
        list($errMsg, $errCode) = self::_formatTpl($errorTpl);
        throw new $customizeException($errMsg, $errCode);
    }
}
