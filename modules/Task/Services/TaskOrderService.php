<?php

namespace Modules\Seller\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Task\Repositories\TaskOrderRepositoryEloquent;

class TaskOrderService extends BaseService
{
    public function __construct(
        TaskOrderRepositoryEloquent $taskOrderRepositoryEloquent
    )
    {
        $this->setRepository($taskOrderRepositoryEloquent);
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function list($userId)
    {

    }

    public function apply($userId, $taskId)
    {

    }

    public function isAllowApply($userId)
    {

    }

    public function assign($currentUserId, $userId, $taskId)
    {

    }

    public function confirm($userId, $taskId, $storeAccount)
    {

    }

    public function doing($userId, $taskId, $orderId)
    {

    }

    public function done($userId, $taskId)
    {

    }

    public function close($userId, $taskId)
    {

    }

    /**
     * @return mixed|TaskOrderRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }
}