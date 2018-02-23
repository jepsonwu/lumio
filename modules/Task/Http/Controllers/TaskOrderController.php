<?php

namespace Modules\Task\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Seller\Services\TaskOrderService;

class TaskOrderController extends ApiBaseController
{
    protected $taskOrderService;

    public function __construct(TaskOrderService $taskOrderService)
    {
        $this->taskOrderService = $taskOrderService;
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
        $result = $this->taskOrderService->list(AuthHelper::user()->id);
        return $this->success($result);
    }

    public function apply(Request $request)
    {
        $this->validate($request, [
            'task_id' => ['bail', 'required', 'integer'],
        ]);

        $taskOrder = $this->taskOrderService->apply(AuthHelper::user()->id, $request->input("task_id"));
        return $this->success($taskOrder);
    }

    public function isAllowApply(Request $request)
    {
        $this->taskOrderService->isAllowApply(AuthHelper::user()->id);

        return $this->success([]);
    }

    public function assign(Request $request)
    {
        $this->validate($request, [
            'task_id' => ['bail', 'required', 'integer'],
            'user_id' => ['bail', 'required', 'integer'],
        ]);

        $taskOrder = $this->taskOrderService->assign(
            AuthHelper::user()->id,
            $request->input("user_id"),
            $request->input("task_id")
        );
        return $this->success($taskOrder);
    }

    public function confirm(Request $request, $id)
    {
        $this->validate($request, [
            'store_account' => ['bail', 'required', 'string'],
        ]);

        $this->taskOrderService->confirm(AuthHelper::user()->id, $id, $request->input("store_account"));
        return $this->success([]);
    }

    public function doing(Request $request, $id)
    {
        $this->validate($request, [
            'order_id' => ['bail', 'required', 'string'],
        ]);

        $this->taskOrderService->doing(AuthHelper::user()->id, $id, $request->input("order_id"));
        return $this->success([]);
    }

    public function done(Request $request, $id)
    {
        $this->taskOrderService->done(AuthHelper::user()->id, $id);
        return $this->success([]);
    }

    public function close(Request $request, $id)
    {
        $this->taskOrderService->close(AuthHelper::user()->id, $id);
        return $this->success([]);
    }
}