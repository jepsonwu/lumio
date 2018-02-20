<?php

namespace Modules\Task\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Seller\Services\TaskService;

class TaskController extends ApiBaseController
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
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
        $result = $this->taskService->list(AuthHelper::user()->id);
        return $this->success($result);
    }

    public function isAllowCreate(Request $request)
    {
        $this->taskService->isAllowCreate(AuthHelper::user()->id);

        return $this->success([]);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'goods_id' => ['bail', 'required', 'integer'],
            'goods_keyword' => ['bail', 'string', 'required', 'between:1,100'],
            'total_order_number' => ['bail', 'integer', 'required', 'min:1'],
            'platform' => ['bail', 'integer', 'required', 'in:1,2'],
        ]);

        $store = $this->taskService->create(AuthHelper::user()->id, $this->requestParams->getRegularParams());
        return $this->success($store);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'total_order_number' => ['bail', 'required', 'integer', 'min:0'],
        ]);

        $store = $this->taskService->update(
            AuthHelper::user()->id,
            $id,
            (int)$request->get("total_order_number", 0)
        );
        return $this->success($store);
    }

    public function close(Request $request, $id)
    {
        $this->taskService->close(AuthHelper::user()->id, $id);

        return $this->success([]);
    }
}