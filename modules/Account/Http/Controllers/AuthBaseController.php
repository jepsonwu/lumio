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

class AuthBaseController extends ApiBaseController
{
    public function saveLoginInfo($authUserInfo)
    {
        if (isset($authUserInfo['_token'])) {
            $this->addCookie('tg_auth', $authUserInfo['_token'], 2592000 - 1800);
        }
        if (isset($authUserInfo['_auth'])) {
            $this->addCookie('_aries', $authUserInfo['_auth'], 2592000 - 1800);
        }
    }
}