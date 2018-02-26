<?php

namespace Modules\Account\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Account\Services\UserService;

class UserController extends ApiBaseController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *
     *
     * @api {GET} /api/user/v1 我的用户详情
     * @apiSampleRequest /api/user/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user
     * @apiName my-detail
     **
     * @apiError  20113
     *
     * @apiSuccess {int} id
     * @apiSuccess {string} username
     * @apiSuccess {string} avatar
     * @apiSuccess {string} mobile
     * @apiSuccess {int} gender 性别：0-女，1-男，2-未知
     * @apiSuccess {string} qq
     * @apiSuccess {string} email
     * @apiSuccess {string} invite_code
     * @apiSuccess {int} role 角色：0-普通，1-买家，2-卖家
     * @apiSuccess {int} level
     * @apiSuccess {int} open_status 任务开启状态：0-否，1-是
     * @apiSuccess {string} taobao_account
     * @apiSuccess {string} jd_account
     * @apiSuccess {string} token
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":{"id":"10","username":"","avatar":"","mobile":"18258438129","gender":"2","qq":"","email":"","invite_code":"1QKXVDDaAb","role":"0","level":"1","open_status":"0","taobao_account":"","jd_account":"","token":"d49632796366b8d842e78400a3fe4d35","created_at":"1518423978"},"code":"0","msg":"","time":"1518427587"}
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myDetail(Request $request)
    {
        $user = $this->userService->getById(AuthHelper::user()->id);
        return $this->success($user);
    }

    /**
     *
     *
     * @api {GET} /api/user/v1/{id} 别人的用户详情
     * @apiSampleRequest /api/user/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user
     * @apiName user-detail
     *
     * @apiParam {int} id
     **
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":{"id":"10","username":"","avatar":"","mobile":"18258438129","gender":"2","qq":"","email":"","invite_code":"1QKXVDDaAb","role":"0","level":"1","open_status":"0","taobao_account":"","jd_account":"","token":"d49632796366b8d842e78400a3fe4d35","created_at":"1518423978"},"code":"0","msg":"","time":"1518427587"}
     *
     *
     * @param Request $request
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function userDetail(Request $request, $userId)
    {
        return $this->success($this->userService->isValidById($userId));
    }

    /**
     *
     *
     * @api {PUT} /api/user/v1 我的用户详情
     * @apiSampleRequest /api/user/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user
     * @apiName my-detail
     *
     * @apiParam {string} username
     * @apiParam {string} avatar
     * @apiParam {int} gender 0-女，1-男，2-未知
     * @apiParam {string} qq
     * @apiParam {string} email
     * @apiParam {int} open_status 0-否，1-是
     * @apiParam {string} taobao_account
     * @apiParam {string} jd_account
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":{"id":"10","username":"","avatar":"","mobile":"18258438129","gender":"2","qq":"","email":"","invite_code":"1QKXVDDaAb","role":"0","level":"1","open_status":"0","taobao_account":"","jd_account":"","token":"d49632796366b8d842e78400a3fe4d35","created_at":"1518423978"},"code":"0","msg":"","time":"1518427587"}
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(Request $request)
    {
        $this->validate($request, $rules = [//todo 默认值
            "username" => ['bail', 'string', 'between:1,50'],
            "avatar" => ['bail', 'string', 'between:1,150'],
            "gender" => ["bail", "in:0,1,2"],
            "qq" => ['bail', 'string', 'between:1,50'],
            "email" => ['bail', 'string', 'between:1,100'],
            "open_status" => ["bail", "in:0,1"],
            "taobao_account" => ['bail', 'string', 'between:1,100'],
            "jd_account" => ['bail', 'string', 'between:1,100']
        ]);

        $requestParams = $this->requestParams->getRegularParams();
        $user = $this->userService->update(AuthHelper::user(), $requestParams);
        return $this->success($user);
    }
}