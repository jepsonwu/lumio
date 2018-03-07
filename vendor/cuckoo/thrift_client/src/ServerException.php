<?php

namespace Jiuyan\Cuckoo\ThriftClient;

use Exception;
use ReflectionClass;
use Thrift\Exception\TApplicationException;

class ServerException extends Exception
{
    private static $codeToMessageMap = array(
        TApplicationException::UNKNOWN => '未知错误',
        TApplicationException::UNKNOWN_METHOD => '未知方法名',
        TApplicationException::INVALID_MESSAGE_TYPE => '无效信息类型',
        TApplicationException::WRONG_METHOD_NAME => '错误方法名',
        TApplicationException::BAD_SEQUENCE_ID => '无效序列ID',
        TApplicationException::MISSING_RESULT => '结果缺失',
        TApplicationException::INTERNAL_ERROR => 'thrift内部错误',
        TApplicationException::PROTOCOL_ERROR => '协议错误',
        TApplicationException::INVALID_TRANSFORM => '无效转换',
        TApplicationException::INVALID_PROTOCOL => '无效协议',
        TApplicationException::UNSUPPORTED_CLIENT_TYPE => '不支持的客户端类型',
    );

    public function getErrorName()
    {
        $tApplicationException = new ReflectionClass('Thrift\Exception\TApplicationException');
        $constants = $tApplicationException->getConstants();
        $constName = 'UNKNOWN';
        foreach ($constants as $n => $v) {
            if ($v === $this->getCode()) {
                $constName = $n;
                break;
            }
        }
        return $constName;
    }
}
