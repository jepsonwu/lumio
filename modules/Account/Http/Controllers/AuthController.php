<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
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

    /**
     *
     *
     * @api {GET} /api/account/v1/sms-captcha 发送短信验证码
     * @apiSampleRequest /api/account/v1/sms-captcha
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup account
     * @apiName sms-captcha
     *
     * @apiParam {string} mobile
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1517818507"}
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSmsCaptcha(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => 'required|mobile',
            ]
        );

        $this->accountService->sendAccountCaptcha($request->input("mobile"));

        return $this->success([]);
    }


    public function register(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => ['bail', 'required', 'mobile'],
                'code' => 'bail|required|integer',
                'password' => ['bail', 'required', 'string'],//长度
            ]
        );

        $user = $this->accountService->register($this->requestParams->getRegularParams());
        $this->saveLoginInfo($user);

        return $this->success($user);
    }

    public function login(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => ['bail', 'required', 'mobile'],
                //'username' => 'required|string',
                'password' => 'required|string'
            ]
        );

        $user = $this->accountService->login($request->input("mobile"), $request->input("password"));
        $this->saveLoginInfo($user);

        return $this->success($user);
    }

    public function logout()
    {
        $this->accountService->logout();
        return $this->success([]);
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

        $this->accountService->changePassword(Auth::user(), $request->input("password"), $request->input("new_password"));

        return $this->success([]);
    }

    public function resetPassword(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => 'bail|required|mobile',
                'code' => 'bail|required|integer',
                'password' => 'bail|required|string'
            ]
        );

        $this->accountService->resetPassword($request->input("mobile"), $request->input("password"), $request->input("code"));
        return $this->success([]);
    }
}
