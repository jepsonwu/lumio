<?php

namespace Modules\Admin\Http\Controllers\Task;

use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Task\Models\Task;
use Modules\Task\Services\TaskInternalService;

class TaskController extends AdminController
{

    protected $title = '任务管理';

    protected $taskInternalService;

    public function __construct(TaskInternalService $taskInternalService)
    {
        $this->taskInternalService = $taskInternalService;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $this->subTitle = '任务列表';

        $this->validate($request, $rules = [
            "user_id" => ["integer"],
            "store_id" => ["integer"],
            "platform" => ["in:-1,1,2"],
            "task_status" => ["in:-1,1,2,3,4"],
        ]);


        $conditions = [];
        $params = $request->only(array_keys($rules));
        $params['platform'] == "" && $params['platform'] = '-1';
        $params['task_status'] == "" && $params['task_status'] = '-1';

        $params['user_id'] && $conditions[] = ["user_id", $params['user_id']];
        $params['store_id'] && $conditions[] = ["store_id", $params['store_id']];
        $params['platform'] != -1 && $conditions[] = ["platform", $params['platform']];
        $params['task_status'] != -1 && $conditions[] = ["task_status", $params['task_status']];

        $query = Task::query();
        $list = $query->where($conditions)->paginate(10);

        return $this->render('admin/task/index', [
            'list' => $list,
            'params' => $params
        ]);
    }
}