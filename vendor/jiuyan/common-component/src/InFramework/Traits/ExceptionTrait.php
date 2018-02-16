<?php

namespace Jiuyan\Common\Component\InFramework\Traits;

use Jiuyan\Common\Component\InFramework\Exceptions\DBException;

trait ExceptionTrait
{
    public function throwDBExceptionByFalse($result, $message, $code = 0)
    {
        if ($result === false) {
            throw new DBException($message, $code);
        }
    }

    public function throwDBException($result, $message, $code = 0)
    {
        if (!$result) {
            throw new DBException($message, $code);
        }
    }
}