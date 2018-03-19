<?php

namespace Modules\Task\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Task\Services\TaskOrderService;

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
     * @api {GET} /api/task-order/v1 任务订单列表
     * @apiSampleRequest /api/task-order/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName list
     *
     * @apiParam {int} [order_status] 状态
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"current_page":"1","data":[{"id":"1","user_id":"10","task_id":"2","task_user_id":"10","order_id":"","order_status":"1","created_at":"1519827187"}],"from":"1","last_page":"1","next_page_url":"","path":"http:\/\/test.lumio.com\/api\/task-order\/v1","per_page":"10","prev_page_url":"","to":"1","total":"1"},"code":"0","msg":"","time":"1519827309"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        $this->validate($request, [
            'order_status' => ['in:1,2,3,4'],
        ]);

        $params = $this->requestParams->getRegularParams();
        $params = array_filter($params, function ($val) {
            return $val != "";
        });

        $conditions = [
            "user_id" => AuthHelper::user()->id,
        ];
        isset($params['order_status']) && $conditions['order_status'] = $params['order_status'];

        $result = $this->taskOrderService->list($conditions);
        return $this->success($result);
    }

    /**
     *
     *
     * @api {POST} /api/task-order/v1 申请任务订单
     * @apiSampleRequest /api/task-order/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName apply
     *
     * @apiParam {int} task_id 任务ID
     *
     * @apiError  20113
     *
     * @apiSuccess {int} id
     * @apiSuccess {int} user_id
     * @apiSuccess {int} task_id
     * @apiSuccess {int} task_user_id
     * @apiSuccess {int} order_status 订单状态：1-waiting，2-doing，3-done，4-close
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"user_id":"10","task_id":"2","task_user_id":"10","order_status":"1","created_at":"1519827187","id":"1"},"code":"0","msg":"","time":"1519827187"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function apply(Request $request)
    {
        $this->validate($request, [
            'task_id' => ['bail', 'required', 'integer'],
        ]);

        $taskOrder = $this->taskOrderService->apply(AuthHelper::user()->id, $request->input("task_id"));
        return $this->success($taskOrder);
    }

    /**
     *
     *
     * @api {GET} /api/task-order/v1/check-permission 检查申请权限
     * @apiSampleRequest /api/task-order/v1/check-permission
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName check-permission
     *
     * @apiParam {int} task_id 任务ID
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkPermission(Request $request)
    {
        $this->validate($request, [
            'task_id' => ['bail', 'required', 'integer'],
        ]);

        $this->taskOrderService->checkPermission(AuthHelper::user()->id, $request->input("task_id"));

        return $this->success([]);
    }

    /**
     *
     *
     * @api {PUT} /api/task-order/v1/verify/{id} 审核任务订单
     * @apiSampleRequest /api/task-order/v1/verify/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName verify
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function verify(Request $request, $id)
    {
        $this->taskOrderService->verify(AuthHelper::user()->id, $id);
        return $this->success([]);
    }

    /**
     *
     *
     * @api {POST} /api/task-order/v1/assign 派发任务订单
     * @apiSampleRequest /api/task-order/v1/assign
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName assign
     *
     * @apiParam {int} task_id 任务ID
     * @apiParam {int} user_id 用户ID
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"user_id":"10","task_id":"2","task_user_id":"10","order_status":"1","created_at":"1519827187","id":"1"},"code":"0","msg":"","time":"1519827187"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
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

    /**
     *
     *
     * @api {POST} /api/task-order/v1/confirm/{id} 确认任务订单
     * @apiSampleRequest /api/task-order/v1/confirm/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName confirm
     *
     * @apiParam {string} store_account 店铺账号
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirm(Request $request, $id)
    {
        $this->validate($request, [
            'store_account' => ['bail', 'required', 'string'],
        ]);

        $this->taskOrderService->confirm(AuthHelper::user()->id, $id, $request->input("store_account"));
        return $this->success([]);
    }

    /**
     *
     *
     * @api {PUT} /api/task-order/v1/{id} 做任务订单
     * @apiSampleRequest /api/task-order/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName doing
     *
     * @apiParam {string} order_id 订单号
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function doing(Request $request, $id)
    {
        $this->validate($request, [
            'order_id' => ['bail', 'required', 'string'],
        ]);

        $this->taskOrderService->doing(AuthHelper::user()->id, $id, $request->input("order_id"));
        return $this->success([]);
    }

    /**
     *
     *
     * @api {PUT} /api/task-order/v1/seller-confirm/{id} 卖家确认任务订单
     * @apiSampleRequest /api/task-order/v1/seller-confirm/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName seller-confirm
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sellerConfirm(Request $request, $id)
    {
        $this->taskOrderService->sellerConfirm(AuthHelper::user()->id, $id);
        return $this->success([]);
    }

    /**
     *
     *
     * @api {PUT} /api/task-order/v1/buyer-confirm/{id} 买家确认任务订单
     * @apiSampleRequest /api/task-order/v1/buyer-confirm/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName buyer-confirm
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buyerConfirm(Request $request, $id)
    {
        $this->taskOrderService->buyerConfirm(AuthHelper::user()->id, $id);
        return $this->success([]);
    }

    /**
     *
     *
     * @api {PUT} /api/task-order/v1/done/{id} 完成任务订单
     * @apiSampleRequest /api/task-order/v1/done/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName done
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function done(Request $request, $id)
    {
        $this->taskOrderService->done(AuthHelper::user()->id, $id);
        return $this->success([]);
    }

    /**
     *
     *
     * @api {DELETE} /api/task-order/v1/{id} 删除任务订单
     * @apiSampleRequest /api/task-order/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName close
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function close(Request $request, $id)
    {
        $this->taskOrderService->close(AuthHelper::user()->id, $id);
        return $this->success([]);
    }

    /**
     *
     *
     * @api {PUT} /api/task-order/v1/freeze/{id} 冻结任务订单
     * @apiSampleRequest /api/task-order/v1/freeze/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task-order
     * @apiName freeze
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     *{"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function freeze(Request $request, $id)
    {
        $this->taskOrderService->freeze(AuthHelper::user()->id, $id);
        return $this->success([]);
    }
}