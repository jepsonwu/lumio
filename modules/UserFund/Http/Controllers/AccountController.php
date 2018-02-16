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
     * @api {GET} /api/user-fund/v1 我的资金账户
     * @apiSampleRequest /api/user-fund/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund
     * @apiName list
     **
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *
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
     * @api {POST} /api/user-fund/v1 创建资金账户
     * @apiSampleRequest /api/user-fund/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund
     * @apiName create
     **
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *
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
     * @api {PUT} /api/user-fund/v1/{id} 修改资金账户
     * @apiSampleRequest /api/user-fund/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund
     * @apiName update
     **
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $id)
    {
        return $this->success([]);
    }

    /**
     *
     *
     * @api {DELETE} /api/user-fund/v1/{id} 删除资金账户
     * @apiSampleRequest /api/user-fund/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup user-fund
     * @apiName delete
     **
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, $id)
    {
        return $this->success([]);
    }
}