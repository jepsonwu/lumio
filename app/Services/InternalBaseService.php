<?php
namespace App\Services;

class InternalBaseService
{
    function formatSucc($data, $msg = '', $code = 0)
    {
        return [
            'succ' => true,
            'data' => $data,
            'msg' => $msg,
            'code' => $code,

        ];
    }

    function formatError($data, $msg = '', $code = 0)
    {
        return [
            'succ' => false,
            'data' => $data,
            'msg' => $msg,
            'code' => $code,

        ];
    }
}
