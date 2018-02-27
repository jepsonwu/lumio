<?php

namespace Modules\Task\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;

class TaskInternalService extends BaseService
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function checkActiveByGoods($goodsId)
    {
        return $this->taskService->checkActiveByGoods($goodsId);
    }

    public function checkActiveByStore($storeId)
    {
        return $this->taskService->checkActiveByStore($storeId);
    }
}