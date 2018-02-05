<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Account\Constants\AccountBusinessConstant;
use Modules\Account\Constants\AccountErrorConstant;
use Modules\Account\Constants\AccountResponseCodeConstant;
use Modules\Account\Services\AccountRequestService;
use Auth;

class AuthController extends AuthBaseController
{
    /**
     * @var AccountRequestService
     */
    public $accountService;

    public function __construct(AccountRequestService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function getSmsCaptcha(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => 'required', //因为安卓客户端本地没有做mobile格式校验，如果手机号格式有误，需要返回特点的错误信息才能触发toast；因此这里不做系统层面的校验
                'business_type' => 'string'
            ]
        );
        $this->requestParams->setRegularParam('captcha_type', 'sms');
        if ($this->accountService->sendAccountCaptcha($this->requestParams->getRegularParams())) {
            return $this->result(true, ['voice_code_text' => '你将会收到来自in（125909888389）含有语音验证码的电话']);
        }
        return $this->error(AccountErrorConstant::ERR_ACCOUNT_SMS_CODE_SEND_FAILED);
    }

    public function register(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => ['bail', 'required', 'mobile'],
                'code' => 'bail|required|integer',
                'password' => ['bail', 'required', 'string'],
                'force' => 'sometimes|required|integer',
            ]
        );

        list($responseData, $codeTpl) = $this->accountService->registerUser($this->requestParams->getRegularParams());
        $this->saveLoginInfo($responseData);
        return $this->result(true, $responseData, $codeTpl);
    }

    public function login(Request $request)
    {
        $this->validate(
            $request,
            [
                'username' => 'required|string',
                'password' => 'required|string'
            ]
        );
        list($loginUser, $responseCodeTpl) = $this->accountService->loginCommonAccount($this->requestParams->getRegularParams());
        if (!$responseCodeTpl) {
            $this->saveLoginInfo($loginUser);
            return $this->success($loginUser);
        }
        return $this->result(false, $loginUser, $responseCodeTpl);
    }

    public function logout()
    {

    }

    public function setPassword(Request $request)
    {
        $this->validate($request, ['password' => 'required|string']);
        $this->requestParams->setRegularParam('currentUser', Auth::user());
        $this->accountService->setAccountPassword($this->requestParams->getRegularParams());
        return $this->result(true, [], AccountResponseCodeConstant::COMMON_ACCOUNT_PASSWORD_SET_SUCCESS);
    }

    public function changePassword(Request $request)
    {
        $this->validate(
            $request,
            [
                'password' => 'required|string',
                'new_password' => 'required|string',
            ]
        );
        $this->requestParams->setRegularParam('currentUser', Auth::user());
        $accountChangeInfo = $this->accountService->changeAccountPassword($this->requestParams->getRegularParams());
        $this->saveLoginInfo($accountChangeInfo);
        return $this->result(true, $accountChangeInfo, AccountResponseCodeConstant::COMMON_ACCOUNT_PASSWORD_CHANGE_SUCCESS);
    }

    public function resetPassword(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => 'required|mobile',
                'code' => 'required|integer',
                'password' => 'required|string'
            ]
        );
        if ($this->accountService->resetAccountPassword($this->requestParams->getRegularParams())) {
            return $this->result(true, [], AccountResponseCodeConstant::COMMON_ACCOUNT_PASSWORD_RESET_SUCCESS);
        }
        return $this->error(AccountErrorConstant::ERR_ACCOUNT_PASSWORD_RESET_FAILED);
    }
}
