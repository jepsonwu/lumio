<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/9/18
 * Time: 15:11
 */

namespace App\Components;

interface ResponseGlobalInterface
{
    public function response($responseStatus = true, $responseData = [], $responseCode = 0, $responseMsg = '');
    public function success($responseData = [], $responseCode = 0, $responseMsg = '');
    public function error($responseTpl = '1|error');
}