<?php

namespace Modules\Seller\Services;

use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Task\Constants\TaskErrorConstant;
use Modules\Task\Models\Task;
use Modules\Task\Models\TaskOrder;
use Modules\Task\Repositories\TaskOrderRepositoryEloquent;
use Modules\Account\Services\UserInternalService;
use Modules\UserFund\Services\UserFundInternalService;

class TaskOrderService extends BaseService
{
    protected $_taskService;
    protected $_userInternalService;
    protected $_userFundInternalService;
    protected $_sellerInternalService;

    public function __construct(
        TaskService $taskService,
        TaskOrderRepositoryEloquent $taskOrderRepositoryEloquent,
        UserInternalService $userInternalService,
        UserFundInternalService $userFundInternalService,
        SellerInternalService $sellerInternalService
    )
    {
        $this->_taskService = $taskService;
        $this->setRepository($taskOrderRepositoryEloquent);
        $this->_userInternalService = $userInternalService;
        $this->_userFundInternalService = $userFundInternalService;
        $this->_sellerInternalService = $sellerInternalService;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function list($userId)
    {

    }

    public function apply($userId, $taskId)
    {
        $task = $this->_taskService->isValidApplyTask($taskId);
        $this->isAllowApply($userId, $task);

        return $this->doingTransaction(function () use ($userId, $task) {
            $taskOrder = $this->rawCreate($userId, $task->id);

            $this->throwDBException(
                $this->_taskService->incWaitingOrder($task),
                "增加任务表等待订单失败"
            );

            return $taskOrder;
        }, new Collection([
            $this->getRepository(),
            $this->_taskService->getRepository()
        ]), TaskErrorConstant::ERR_TASK_ORDER_APPLY_FAILED);
    }

    /**
     * @param $userId
     * @param $taskId
     * @return mixed|TaskOrder
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\DBException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    protected function rawCreate($userId, $taskId)
    {
        $attributes = [
            "user_id" => $userId,
            "task_id" => $taskId,
            "order_status" => TaskOrder::STATUS_WAITING,
            "created_at" => time()
        ];
        $taskOrder = $this->getRepository()->create($attributes);
        $this->throwDBException($taskOrder, "创建订单任务失败");

        return $taskOrder;
    }

    public function checkApply($userId, $taskId)
    {
        $task = $this->_taskService->isValidApplyTask($taskId);

        return $this->isAllowApply($userId, $task);
    }

    public function isAllowApply($userId, Task $task)
    {
        if (!$this->_userInternalService->isBuyer($userId)) {
            $this->_sellerInternalService->isTaobaoStore($task->store_id)
                ? $this->_userInternalService->isDeployTaobaoAccount($userId)
                : $this->_userInternalService->isDeployJdAccount($userId);

            $this->_userFundInternalService->isFinishedDeployAccount($userId);
        }
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