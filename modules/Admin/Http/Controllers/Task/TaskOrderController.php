<?php

namespace Modules\Admin\Http\Controllers\Task;

use App\Components\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Task\Models\TaskOrder;
use Modules\Task\Services\TaskInternalService;

class TaskOrderController extends AdminController
{

    protected $title = '任务订单管理';

    protected $taskInternalService;

    public function __construct(TaskInternalService $taskInternalService)
    {
        $this->taskInternalService = $taskInternalService;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $this->subTitle = '任务订单列表';

        $this->validate($request, $rules = [
            "user_id" => ["integer"],
            "task_id" => ["integer"],
            "task_user_id" => ["integer"],
            "order_status" => ["in:-1,1,2,3,4,5,6,7,8"],
        ]);


        $conditions = [];
        $params = $request->only(array_keys($rules));
        $params['order_status'] == "" && $params['task_status'] = '-1';

        $params['user_id'] && $conditions[] = ["user_id", $params['user_id']];
        $params['task_id'] && $conditions[] = ["task_id", $params['task_id']];
        $params['task_user_id'] && $conditions[] = ["task_user_id", $params['task_user_id']];
        $params['order_status'] != -1 && $conditions[] = ["order_status", $params['order_status']];

        $query = TaskOrder::query();
        $list = $query->where($conditions)->paginate(10);

        return $this->render('admin/task-order/index', [
            'list' => $list,
            'params' => $params
        ]);
    }

    public function unFreeze(Request $request, $id)
    {
        $this->taskInternalService->unFreezeTaskOrder(AuthHelper::user()->id, $id);

        return $this->success([]);
    }
}