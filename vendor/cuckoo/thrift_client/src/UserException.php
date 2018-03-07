<?php

namespace Jiuyan\Cuckoo\ThriftClient;
use Exception;

class UserException extends Exception
{
    const SERVICE_DOWNGRADE_MESSAGE = 'Service Unavailable';
    const SERVICE_DOWNGRADE_CODE = '400';
    const SERVICE_DOWNGRADE_ERROR_NAME = 'SERVICE_UNAVAILABLE';

    private $errorName;

    public function __construct($message, $code, $errorName)
    {
        $this->errorName = $errorName;
        parent::__construct($message, $code);
    }

    public function getErrorName()
    {
        return $this->errorName;
    }

    public static function serviceDowngrade()
    {
        throw new static(self::SERVICE_DOWNGRADE_MESSAGE, self::SERVICE_DOWNGRADE_CODE, self::SERVICE_DOWNGRADE_ERROR_NAME);
    }
}
