<?php
/**
 * Created by PhpStorm.
 * User: feraner
 * Date: 2017/12/1
 * Time: 下午3:07
 */

namespace Modules\User\Http\Controllers;

use Auth;
use Dingo\Api\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;

class ResourcesController extends ApiBaseController
{

    public function ping(Request $request)
    {
        dd(Auth::guard()->user());

    }


    public function adminTest()
    {
        dd(Auth::user());
    }



}