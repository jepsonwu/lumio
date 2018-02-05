<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/24
 * Time: 10:33
 */

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Account\Services\AccountRequestService;
use Modules\Account\Services\UserService;

class AuthBaseController extends ApiBaseController
{
    public function saveLoginInfo($authUserInfo)
    {
        if (isset($authUserInfo['_token'])) {
            $this->addCookie('_token', $authUserInfo['token'], UserService::TOKEN_EXPIRES);
        }
        if (isset($authUserInfo['_auth'])) {
            $this->addCookie('_aries', $authUserInfo['_auth'], 2592000 - 1800);
        }
    }
}