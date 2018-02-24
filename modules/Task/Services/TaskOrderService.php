<?php

namespace Modules\Seller\Services;

use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Task\Constants\TaskErrorConstant;
use Modules\Task\Models\Task;
use Modules\Task\Models\TaskOrder;
use Modules\Task\Repositories\TaskOrderRepositoryEloquent;
use Modules\Account\Services\UserInternalService;
use Modules\UserFund\Services\UserFundInternalService;

class TaskOrderService extends BaseService
{
    const COMMISSION_PERCENT = 10;//万分之几

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
        $this->checkPermission($userId, $taskId);

        return $this->doingTransaction(function () use ($userId, $taskId) {
            $task = $this->_taskService->isValidApplyTask($taskId);
            $taskOrder = $this->rawCreate($userId, $task);

            $this->throwDBException(
                $this->_taskService->incWaitingOrder($task),
                "增加任务表订单失败"
            );

            return $taskOrder;
        }, new Collection([
            $this->getRepository(),
            $this->_taskService->getRepository()
        ]), TaskErrorConstant::ERR_TASK_ORDER_APPLY_FAILED);
    }

    /**
     * @param $userId
     * @param Task $task
     * @return mixed|TaskOrder
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\DBException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    protected function rawCreate($userId, Task $task)
    {
        $attributes = [
            "user_id" => $userId,
            "task_id" => $task->id,
            "task_user_id" => $task->user_id,
            "order_status" => TaskOrder::STATUS_WAITING,
            "created_at" => time()
        ];
        $taskOrder = $this->getRepository()->create($attributes);
        $this->throwDBException($taskOrder, "创建订单任务失败");

        return $taskOrder;
    }

    public function checkPermission($userId, $taskId)
    {
        if ($this->_userInternalService->isBuyer($userId)) {
            return true;
        }

        $task = $this->_taskService->isValidTask($taskId);
        $this->_sellerInternalService->isTaobaoStore($task->store_id)
            ? $this->_userInternalService->isDeployTaobaoAccount($userId)
            : $this->_userInternalService->isDeployJdAccount($userId);

        return true;
    }

    public function assign($currentUserId, $userId, $taskId)
    {
        $this->checkPermission($userId, $taskId);
        $this->isAllowAssign($userId);

        return $this->doingTransaction(function () use ($currentUserId, $userId, $taskId) {
            $task = $this->_taskService->isMyValidApplyTask($currentUserId, $taskId);
            $taskOrder = $this->rawCreate($userId, $task);

            $this->throwDBException(
                $this->_taskService->incWaitingOrder($task),
                "增加任务表订单失败"
            );

            return $taskOrder;
        }, new Collection([
            $this->getRepository(),
            $this->_taskService->getRepository()
        ]), TaskErrorConstant::ERR_TASK_ORDER_ASSIGN_FAILED);
    }

    protected function isAllowAssign($userId)
    {
        $this->_userInternalService->isAutoApplyTask($userId)
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_DISALLOW_ASSIGN_USER);
    }

    public function confirm($userId, $taskOrderId, $storeAccount)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowOperate($userId, $taskOrder);

        $task = $this->_taskService->isValidTask($taskOrder->task_id);
        $store = $this->_sellerInternalService->isValidStore($task->store_id);

        $store->store_account == $storeAccount
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_CONFIRM_FAILED);

        return true;
    }

    protected function isAllowOperate($userId, TaskOrder $taskOrder)
    {
        $userId == $taskOrder->user_id
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_OPERATE_ILLEGAL);
    }

    public function doing($userId, $taskOrderId, $orderId)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowDoing($userId, $taskOrder);

        return $this->doingTransaction(function () use ($taskOrder, $orderId) {
            $task = $this->_taskService->isValidTask($taskOrder->task_id, true);
            $this->throwDBException(
                $this->getRepository()->doing($taskOrder, $orderId),
                "做任务失败"
            );

            $this->throwDBException(
                $this->_taskService->incDoingOrder($task),
                "增加任务表订单失败"
            );

            return true;
        }, new Collection([
            $this->getRepository(),
            $this->_taskService->getRepository()
        ]), TaskErrorConstant::ERR_TASK_ORDER_DOING_FAILED);
    }

    protected function isAllowDoing($userId, TaskOrder $taskOrder)
    {
        $this->isAllowOperate($userId, $taskOrder);
        $taskOrder->isWaiting()
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_DISALLOW_DOING);
    }

    public function done($userId, $taskOrderId)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowDone($userId, $taskOrder);

        return $this->doingTransaction(function () use ($userId, $taskOrder) {
            $task = $this->_taskService->isValidTask($taskOrder->task_id, true);
            $this->throwDBException(
                $this->getRepository()->done($taskOrder),
                "完成任务失败"
            );

            $this->throwDBException(
                $this->_taskService->incDoneOrder($task),
                "增加完成任务失败"
            );

            $this->_userFundInternalService->pay($userId, $task->goods_price, "");

            $this->_userFundInternalService->earn(
                $taskOrder->user_id,
                $task->goods_price,
                $this->makeCommission($task->goods_price),
                ""
            );

            return true;
        }, new Collection([
            $this->getRepository(),
            $this->_taskService->getRepository()
        ]), TaskErrorConstant::ERR_TASK_ORDER_DONE_FAILED);
    }

    protected function isAllowDone($userId, TaskOrder $taskOrder)
    {
        $this->isAllowDoneOperate($userId, $taskOrder);
        $taskOrder->isDoing()
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_DISALLOW_DONE);
    }

    protected function makeCommission($amount)
    {
        return self::COMMISSION_PERCENT * $amount / 10000;
    }

    protected function isAllowDoneOperate($userId, TaskOrder $taskOrder)
    {
        $userId == $taskOrder->task_user_id
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_OPERATE_ILLEGAL);
    }

    public function close($userId, $taskOrderId)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowOperate($userId, $taskOrder);
        $this->isAllowClose($taskOrder);


        return $this->doingTransaction(function () use ($userId, $taskOrder) {
            $task = $this->_taskService->isValidTask($taskOrder->task_id, true);
            $this->throwDBException(
                $this->getRepository()->close($taskOrder),
                "完成任务失败"
            );

            $this->throwDBException(
                (
                $taskOrder->isWaiting()
                    ? $this->_taskService->decWaitingOrder($task)
                    : $this->_taskService->decDoingOrder($task)
                ),
                "增加完成任务失败"
            );

            return true;
        }, new Collection([
            $this->getRepository(),
            $this->_taskService->getRepository()
        ]), TaskErrorConstant::ERR_TASK_ORDER_CLOSE_FAILED);
    }

    protected function isAllowClose(TaskOrder $taskOrder)
    {
        $taskOrder->isAllowClose()
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_DISALLOW_CLOSE);
    }

    /**
     * @param $taskId
     * @return Collection|mixed|\Prettus\Repository\Database\Eloquent\Model|TaskOrder
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function isValidTaskOrder($taskId)
    {
        $taskOrder = $this->getRepository()->find($taskId);
        $taskOrder || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_INVALID);

        return $taskOrder;
    }

    /**
     * @return mixed|TaskOrderRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }
}