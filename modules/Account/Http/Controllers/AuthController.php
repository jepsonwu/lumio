<?php

namespace Modules\Account\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Modules\Account\Services\AccountService;

class AuthController extends AuthBaseController
{
    /**
     * @var AccountService
     */
    public $accountService;

    public function __construct(AccountService $accountService)
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

    /**
     *
     *
     * @api {POST} /api/account/v1/register 注册
     * @apiSampleRequest /api/account/v1/register
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup account
     * @apiName register
     *
     * @apiParam {string} mobile
     * @apiParam {int} captcha 验证码
     * @apiParam {string} password
     * @apiParam {string} confirm_password
     * @apiParam {string} invite_code 邀请码
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":{"mobile":"18258438129","gender":"2","role":"0","open_status":"0","invite_code":"1YAvMQvpBo","token":"9110ba4be2415257d100b35ec231bcc4","created_at":"1518423619","id":"9"},"code":"0","msg":"","time":"1518423619"}
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => ['bail', 'required', 'mobile'],
                'captcha' => 'bail|required|integer',
                'password' => ['bail', 'required', 'string', 'between:6,12'],
                'confirm_password' => ['bail', 'required', 'string', 'between:6,12'],
                'invite_code' => ['bail', 'string']
            ]
        );

        $requestParams = $this->requestParams->getRegularParams();
        isset($requestParams['invite_code']) || $requestParams['invite_code'] = "";
        $user = $this->accountService->register($requestParams);
        $this->saveLoginInfo($user);

        return $this->success($user);
    }

    /**
     *
     *
     * @api {POST} /api/account/v1/login 登录
     * @apiSampleRequest /api/account/v1/login
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup account
     * @apiName login
     *
     * @apiParam {string} mobile
     * @apiParam {string} password
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":{"mobile":"18258438129","gender":"2","role":"0","open_status":"0","invite_code":"1YAvMQvpBo","token":"9110ba4be2415257d100b35ec231bcc4","created_at":"1518423619","id":"9"},"code":"0","msg":"","time":"1518423619"}
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => ['bail', 'required', 'mobile'],
                'password' => ['bail', 'required', 'string', 'between:6,12'],
            ]
        );

        $user = $this->accountService->login($request->input("mobile"), $request->input("password"));
        $this->saveLoginInfo($user);

        return $this->success($user);
    }

    /**
     *
     *
     * @api {POST} /api/account/v1/logout 退出
     * @apiSampleRequest /api/account/v1/logout
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup account
     * @apiName logout
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1518425196"}
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout()
    {
        $this->addCookie('_token', AuthHelper::user()->token, -1, '.lumio.com');//todo optimize
        return $this->success([]);
    }

    /**
     *
     *
     * @api {PUT} /api/account/v1/password 修改密码
     * @apiSampleRequest /api/account/v1/password
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup account
     * @apiName password
     *
     * @apiParam {string} password
     * @apiParam {string} new_password
     * @apiParam {string} new_confirm_password
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1518425196"}
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePassword(Request $request)
    {
        $this->validate(
            $request,
            [
                'password' => ['bail', 'required', 'string', 'between:6,12'],
                'new_password' => ['bail', 'required', 'string', 'between:6,12'],
                'new_confirm_password' => ['bail', 'required', 'string', 'between:6,12'],
            ]
        );

        $this->accountService->changePassword(
            AuthHelper::user(), $request->input("password"),
            $request->input("new_password"), $request->input("new_confirm_password")
        );

        return $this->success([]);
    }

    /**
     *
     *
     * @api {POST} /api/account/v1/password/reset 重置密码
     * @apiSampleRequest /api/account/v1/password/reset
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup account
     * @apiName password-reset
     *
     * @apiParam {string} mobile
     * @apiParam {int} captcha 验证码
     * @apiParam {string} password
     * @apiParam {string} confirm_password
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1518425196"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPassword(Request $request)
    {
        $this->validate(
            $request,
            [
                'mobile' => 'bail|required|mobile',
                'captcha' => 'bail|required|integer',
                'password' => ['bail', 'required', 'string', 'between:6,12'],
                'confirm_password' => ['bail', 'required', 'string', 'between:6,12'],
            ]
        );

        $this->accountService->resetPassword($request->input("mobile"), $request->input("password"),
            $request->input("confirm_password"), $request->input("code")
        );
        return $this->success([]);
    }
}
