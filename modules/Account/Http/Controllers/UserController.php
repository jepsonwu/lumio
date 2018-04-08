<?php

namespace Modules\Account\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use App\Constants\GlobalDBConstant;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Account\Models\User;
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
     * @api {GET} /api/user/v1/assign-list 派发用户列表
     * @apiSampleRequest /api/task/v1/assign-list
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user
     * @apiName assign-list
     *
     * @apiParam {string} [username] 用户名
     * @apiParam {string} [mobile] 手机号
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"current_page":"1","data":[{"id":"10","username":"jepson","avatar":"","mobile":"18258438129","gender":"1","qq":"11","email":"11","invited_user_id":"0","invite_code":"1QKXVDDaAb","role":"1","level":"1","open_status":"1","taobao_account":"","jd_account":"","token":"","created_at":"1518423978"}],"from":"1","last_page":"1","next_page_url":"","path":"http:\/\/test.lumio.com\/api\/user\/v1\/assign-list","per_page":"10","prev_page_url":"","to":"1","total":"1"},"code":"0","msg":"","time":"1523188266"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function assignList(Request $request)
    {
        $this->validate($request, [
            "username" => ['bail', 'string', 'between:1,50'],
            'mobile' => 'mobile',
        ]);

        $params = $this->requestParams->getRegularParams();
        $params = array_filter($params, function ($val) {
            return $val != "";
        });

        $conditions = [
            ["role", "=", User::ROLE_BUYER],
            ["open_status", "=", GlobalDBConstant::DB_TRUE]
        ];
        isset($params['username'])
        && $conditions['username'] = ['username', 'like', "%{$params['username']}%"];
        isset($params['mobile']) && $conditions['mobile'] = $params['mobile'];

        $result = $this->userService->list($conditions);
        return $this->success($result);
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
        $user = $this->userService->isValidById($userId);
        $user = $this->userService->formatSecurity($user);
        return $this->success($user);
    }

    /**
     *
     *
     * @api {PUT} /api/user/v1 修改个人详情
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