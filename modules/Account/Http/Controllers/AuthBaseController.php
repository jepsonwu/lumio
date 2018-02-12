<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/24
 * Time: 10:33
 */

namespace Modules\Account\Http\Controllers;

use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Account\Services\UserService;

class AuthBaseController extends ApiBaseController
{
    public function saveLoginInfo($authUserInfo)
    {
        if (isset($authUserInfo['token'])) {
            $this->addCookie('_token', $authUserInfo['token'], UserService::TOKEN_EXPIRES, '.lumio.com');
        }
        if (isset($authUserInfo['auth'])) {
            $this->addCookie('_aries', $authUserInfo['auth'], 2592000 - 1800, '.lumio.com');
        }
    }
}