<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/9/18
 * Time: 15:24
 */

namespace App\Http\Controllers;

class ApiBaseController extends BaseController
{
    public function success($responseData, $responseCode = 0, $responseMsg = '')
    {
    }

    public function error()
    {
    }
}