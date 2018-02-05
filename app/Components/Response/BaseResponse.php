<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/9/18
 * Time: 15:16
 */

namespace App\Components;

class BaseResponse implements ResponseGlobalInterface
{
    public function response($responseStatus = true, $responseData = [], $responseCode = 0, $responseMsg = '')
    {
        return '';
    }

    public function success($responseData = [], $responseCode = 0, $responseMsg = '')
    {
        return $this->response(true, $responseData, $responseCode, $responseMsg);
    }

    public function error($responseTpl = '')
    {
        $responseInfo = explode('|', $responseTpl);
        $responseInfo = ($responseInfo && count($responseInfo) == 2) ? $responseInfo : [100000, 'error'];
        return $this->response(false, [], $responseInfo[0], $responseInfo[1]);
    }
}