<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/9/18
 * Time: 15:15
 */

namespace App\Components;

class JsonResponse extends BaseResponse
{
    public function response($responseStatus = true, $responseData = [], $responseCode = 0, $responseMsg = '')
    {
        return response()->json(
            [
                'succ' => $responseStatus,
                'data' => $responseData,
                'code' => $responseCode,
                'msg' => $responseMsg,
                'timestamp' => time(),
            ]
        );
    }
}