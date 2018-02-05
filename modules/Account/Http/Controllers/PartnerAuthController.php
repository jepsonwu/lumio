<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/24
 * Time: 10:33
 */

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Account\Services\AccountRequestService;

class PartnerAuthController extends AuthBaseController
{
    /**
     * @var AccountRequestService
     */
    public $accountService;

    public function __construct(AccountRequestService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function commonLogin(Request $request, $partnerFlag)
    {
        $this->validate(
            $request,
            [
                'user_name' => 'required|string',
                'password' => 'required|string'
            ]
        );
        $loginUser = $this->accountService->loginPartnerCommonAccount($this->requestParams->getRegularParams(), $partnerFlag);
        $this->saveLoginInfo($loginUser);
        return $this->success($loginUser);
    }

    public function authWeixin(Request $request, $partnerFlag)
    {
        $this->validate(
            $request,
            [
                'access_token' => 'required|string',
                'expires_in' => 'required|string'
            ]
        );
        $registerUserInfo = $this->accountService->registerPartnerWeixin($this->requestParams->getRegularParams(), $partnerFlag);
        $this->saveLoginInfo($registerUserInfo);
        return $this->result(true, $registerUserInfo);
    }

    public function authIn(Request $request, $partnerFlag)
    {
        $this->validate(
            $request,
            [
                'code' => 'required|string',
            ]
        );
        $registerUserInfo = $this->accountService->loginPartnerInAccount($this->requestParams->getRegularParams(), $partnerFlag);
        $this->saveLoginInfo($registerUserInfo);
        return $this->result(true, $registerUserInfo);
    }
}