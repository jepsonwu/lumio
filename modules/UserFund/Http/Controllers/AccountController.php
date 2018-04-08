<?php

namespace Modules\UserFund\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\UserFund\Services\AccountService;

class AccountController extends ApiBaseController
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     *
     *
     * @api {GET} /api/user-fund/account/v1/system-list 系统资金账户
     * @apiSampleRequest /api/user-fund/account/v1/system-list
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund-account
     * @apiName system-list
     *
     *
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[{"id":"1","user_id":"1","real_name":"jepson","id_card":"123123","bank_card":"3123213","bank":"\u62db\u884c","bankfiliale":"\u62db\u884c"}],"code":"0","msg":"","time":"1523185046"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function systemList(Request $request)
    {
        return $this->success($this->accountService->getSystemList());
    }

    /**
     *
     *
     * @api {GET} /api/user-fund/account/v1 我的资金账户
     * @apiSampleRequest /api/user-fund/account/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund-account
     * @apiName list
     *
     *
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[{"id":"4","user_id":"10","real_name":"\u5434\u5065\u5e73","id_card":"3602221991078362","bank_card":"234234343413134","bank":"\u4e2d\u56fd\u94f6\u884c","bankfiliale":"\u676d\u5dde\u4e5d\u5821\u652f\u884c","account_status":"1","created_at":"1518760738","updated_at":"2018-02-16 13:58:58"}],"code":"0","msg":"","time":"1518760783"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        $result = $this->accountService->list(AuthHelper::user()->id);
        return $this->success($result);
    }

    /**
     *
     *
     * @api {POST} /api/user-fund/account/v1 创建资金账户
     * @apiSampleRequest /api/user-fund/account/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund-account
     * @apiName create
     *
     * @apiParam {string} real_name 真实姓名
     * @apiParam {string} id_card 身份证号码
     * @apiParam {string} bank_card 银行卡
     * @apiParam {string} bank 银行
     * @apiParam {string} bankfiliale 支行
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"real_name":"\u5434\u5065\u5e73","id_card":"3602221991078362","bank_card":"234234343413134","bank":"\u4e2d\u56fd\u94f6\u884c","bankfiliale":"\u676d\u5dde\u4e5d\u5821\u652f\u884c","user_id":"10","created_at":"1518760738","id":"4"},"code":"0","msg":"","time":"1518760738"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'real_name' => ['bail', 'string', 'required', 'between:1,50'],
            'id_card' => ['bail', 'string', 'required', 'between:1,50'],
            'bank_card' => ['bail', 'string', 'required', 'between:1,50'],
            'bank' => ['bail', 'string', 'required', 'between:1,50'],
            'bankfiliale' => ['bail', 'string', 'required', 'between:1,50'],
        ]);

        $account = $this->accountService->create(AuthHelper::user()->id, $this->requestParams->getRegularParams());
        return $this->success($account);
    }

    /**
     *
     *
     * @api {PUT} /api/user-fund/account/v1/{id} 修改资金账户
     * @apiSampleRequest /api/user-fund/account/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund-account
     * @apiName update
     *
     * @apiParam {string} real_name 真实姓名
     * @apiParam {string} id_card 身份证号码
     * @apiParam {string} bank_card 银行卡
     * @apiParam {string} bank 银行
     * @apiParam {string} bankfiliale 支行
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":{"real_name":"\u5434\u5065\u5e73","id_card":"3602221991078362","bank_card":"234234343413134","bank":"\u4e2d\u56fd\u94f6\u884c","bankfiliale":"\u676d\u5dde\u4e5d\u5821\u652f\u884c","user_id":"10","created_at":"1518760738","id":"4"},"code":"0","msg":"","time":"1518760738"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'real_name' => ['bail', 'string', 'required', 'between:1,50'],
            'id_card' => ['bail', 'string', 'required', 'between:1,50'],
            'bank_card' => ['bail', 'string', 'required', 'between:1,50'],
            'bank' => ['bail', 'string', 'required', 'between:1,50'],
            'bankfiliale' => ['bail', 'string', 'required', 'between:1,50'],
        ]);

        $account = $this->accountService->update(
            AuthHelper::user()->id,
            $id,
            $this->requestParams->getRegularParams()
        );
        return $this->success($account);
    }

    /**
     *
     *
     * @api {DELETE} /api/user-fund/account/v1/{id} 删除资金账户
     * @apiSampleRequest /api/user-fund/account/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund-account
     * @apiName delete
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[],"code":"0","msg":"","time":"1518761055"}
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, $id)
    {
        $this->accountService->delete(AuthHelper::user()->id, $id);
        return $this->success([]);
    }
}