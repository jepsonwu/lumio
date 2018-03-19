<?php

namespace Modules\Task\Http\Controllers;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Jiuyan\Common\Component\InFramework\Controllers\ApiBaseController;
use Modules\Task\Models\Task;
use Modules\Task\Services\TaskService;

class TaskController extends ApiBaseController
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     *
     * @api {GET} /api/task/my/v1 我的任务列表
     * @apiSampleRequest /api/task/my/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task
     * @apiName my-list
     *
     * @apiParam {int} [store_id] 店铺ID
     * @apiParam {int} [goods_id] 商品ID
     * @apiParam {int} [platform] 平台
     * @apiParam {int} [task_status] 状态
     * @apiParam {int} [goods_name] 商品名称
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"current_page":"1","data":[{"id":"1","user_id":"10","store_id":"1","goods_id":"1","goods_name":"demo","goods_price":"1","goods_image":"","goods_keyword":"cc","total_order_number":"10","finished_order_number":"0","waiting_order_number":"0","doing_order_number":"0","platform":"1","task_status":"1","created_at":"1519822522"}],"from":"1","last_page":"1","next_page_url":"","path":"http:\/\/test.lumio.com\/api\/task\/v1\/my","per_page":"10","prev_page_url":"","to":"1","total":"1"},"code":"0","msg":"","time":"1519824304"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myList(Request $request)
    {
        $this->validate($request, [
            'store_id' => ['int'],
            'goods_id' => ['int'],
            'platform' => ['in:1,2'],
            'task_status' => ['in:1,2,3,4'],
            'goods_name' => ['string', 'between:1,200'],
        ]);

        $params = $this->requestParams->getRegularParams();
        $params = array_filter($params, function ($val) {
            return $val != "";
        });

        $conditions = [
            "user_id" => AuthHelper::user()->id,
        ];
        isset($params['store_id']) && $conditions['store_id'] = $params['store_id'];
        isset($params['goods_id']) && $conditions['goods_id'] = $params['goods_id'];
        isset($params['platform']) && $conditions['platform'] = $params['platform'];
        isset($params['task_status']) && $conditions['task_status'] = $params['task_status'];
        isset($params['goods_name'])
        && $conditions['goods_name'] = ['goods_name', 'like', "%{$params['goods_name']}%"];


        $result = $this->taskService->list($conditions);
        return $this->success($result);
    }

    /**
     *
     *
     * @api {GET} /api/task/v1 首页任务列表
     * @apiSampleRequest /api/task/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task
     * @apiName list
     *
     * @apiParam {int} [platform] 平台
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"current_page":"1","data":[{"id":"1","user_id":"10","store_id":"1","goods_id":"1","goods_name":"demo","goods_price":"1","goods_image":"","goods_keyword":"cc","total_order_number":"10","finished_order_number":"0","waiting_order_number":"0","doing_order_number":"0","platform":"1","task_status":"1","created_at":"1519822522"}],"from":"1","last_page":"1","next_page_url":"","path":"http:\/\/test.lumio.com\/api\/task\/v1\/my","per_page":"10","prev_page_url":"","to":"1","total":"1"},"code":"0","msg":"","time":"1519824304"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(Request $request)
    {
        $this->validate($request, [
            'platform' => ['in:1,2'],
        ]);

        $params = $this->requestParams->getRegularParams();
        $params = array_filter($params, function ($val) {
            return $val != "";
        });

        $conditions = [
            'task_status' => ['task_status', '<', Task::STATUS_DONE]
        ];
        isset($params['platform']) && $conditions['platform'] = $params['platform'];

        $result = $this->taskService->list($conditions);
        return $this->success($result);
    }

    /**
     *
     *
     * @api {GET} /api/task/check/v1 检查是否有权限创建任务
     * @apiSampleRequest /api/task/check/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task
     * @apiName check
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function isAllowCreate(Request $request)
    {
        $this->taskService->isAllowCreate(AuthHelper::user()->id);

        return $this->success([]);
    }

    /**
     *
     *
     * @api {POST} /api/task/v1 创建任务
     * @apiSampleRequest /api/task/v1
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task
     * @apiName create
     *
     * @apiParam {int} goods_id 商品ID
     * @apiParam {string} task_name 任务名称
     * @apiParam {string} goods_keyword 关键词
     * @apiParam {int} total_order_number 总数
     * @apiParam {int} platform 平台：1-pc，2-mobile
     *
     * @apiError  20113
     *
     * @apiSuccess {int} id
     * @apiSuccess {int} user_id
     * @apiSuccess {int} store_id
     * @apiSuccess {int} goods_id
     * @apiSuccess {string} goods_name
     * @apiSuccess {int} goods_price
     * @apiSuccess {string} goods_image
     * @apiSuccess {int} finished_order_number 完成总订单数
     * @apiSuccess {int} doing_order_number 正在进行订单数
     * @apiSuccess {int} waiting_order_number 等待进行订单数
     * @apiSuccess {int} task_status 任务状态：1-waiting，2-doing，3-done，4-close
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"user_id":"10","store_id":"1","goods_id":"1","goods_name":"demo","goods_price":"1","goods_image":"","finished_order_number":"0","doing_order_number":"0","waiting_order_number":"0","task_status":"1","created_at":"1519822522","id":"1"},"code":"0","msg":"","time":"1519822522"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'goods_id' => ['bail', 'required', 'integer'],
            'goods_keyword' => ['bail', 'string', 'required', 'between:1,100'],
            'task_name' => ['bail', 'string', 'required', 'between:1,200'],
            'total_order_number' => ['bail', 'integer', 'required', 'min:1'],
            'platform' => ['bail', 'integer', 'required', 'in:1,2'],
        ]);

        $store = $this->taskService->create(AuthHelper::user()->id, $this->requestParams->getRegularParams());
        return $this->success($store);
    }

    /**
     *只能修改总订单数、搜索关键字、平台
     *
     * @api {PUT} /api/task/v1/{id} 修改任务
     * @apiSampleRequest /api/task/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task
     * @apiName update
     *
     * @apiParam {string} goods_keyword 关键词
     * @apiParam {int} total_order_number 总数
     * @apiParam {int} platform 平台：1-pc，2-mobile
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":{"user_id":"10","store_id":"1","goods_id":"1","goods_name":"demo","goods_price":"1","goods_image":"","finished_order_number":"0","doing_order_number":"0","waiting_order_number":"0","task_status":"1","created_at":"1519822522","id":"1"},"code":"0","msg":"","time":"1519822522"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'total_order_number' => ['bail', 'required', 'integer', 'min:0'],
            'goods_keyword' => ['bail', 'string', 'required', 'between:1,100'],
            'platform' => ['bail', 'integer', 'required', 'in:1,2'],
        ]);

        $store = $this->taskService->update(
            AuthHelper::user()->id,
            $id,
            $this->requestParams->getRegularParams()
        );
        return $this->success($store);
    }

    /**
     *
     * @api {DELETE } /api/task/v1/{id} 删除任务
     * @apiSampleRequest /api/task/v1/{id}
     *
     * @apiVersion 1.0.0
     *
     * @apiGroup task
     * @apiName delete
     *
     * @apiError  20113
     *
     * @apiSuccessExample {json} Success-Response:
     * {"succ":true,"data":[],"code":"0","msg":"","time":"1519820780"}
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function close(Request $request, $id)
    {
        $this->taskService->close(AuthHelper::user()->id, $id);

        return $this->success([]);
    }
}