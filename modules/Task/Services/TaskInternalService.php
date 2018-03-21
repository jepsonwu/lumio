<?php

namespace Modules\Task\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;

class TaskInternalService extends BaseService
{
    protected $taskService;
    protected $taskOrderService;

    public function __construct(TaskService $taskService, TaskOrderService $taskOrderService)
    {
        $this->taskService = $taskService;
        $this->taskOrderService = $taskOrderService;
    }

    public function checkActiveByGoods($goodsId)
    {
        return $this->taskService->checkActiveByGoods($goodsId);
    }

    public function checkActiveByStore($storeId)
    {
        return $this->taskService->checkActiveByStore($storeId);
    }

    public function unFreezeTaskOrder($adminUserId, $taskOrderId)
    {
        return $this->taskOrderService->unFreeze($adminUserId, $taskOrderId);
    }
}