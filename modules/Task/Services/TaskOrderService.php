<?php

namespace Modules\Task\Services;

use App\Components\Factories\InternalServiceFactory;
use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Task\Constants\TaskBanyanDBConstant;
use Modules\Task\Constants\TaskErrorConstant;
use Modules\Task\Models\Task;
use Modules\Task\Models\TaskOrder;
use Modules\Task\Repositories\TaskOrderRepositoryEloquent;

class TaskOrderService extends BaseService
{
    const COMMISSION_PERCENT = 10;//万分之几

    protected $_taskService;

    public function __construct(
        TaskService $taskService,
        TaskOrderRepositoryEloquent $taskOrderRepositoryEloquent
    )
    {
        $this->_taskService = $taskService;
        $this->setRepository($taskOrderRepositoryEloquent);
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function list($conditions)
    {
        return $this->getRepository()->paginateWithWhere($conditions, 10);
    }

    public function apply($userId, $taskId)
    {
        $this->checkPermission($userId, $taskId);

        return $this->doingTransaction(function () use ($userId, $taskId) {
            $task = $this->_taskService->isValidApplyTask($taskId);
            $taskOrder = $this->rawCreate($userId, $task);

            //申请订单不计数
//            $this->throwDBException(
//                $this->_taskService->incWaitingOrder($task),
//                "增加任务表订单失败"
//            );

            return $taskOrder;
        }, new Collection([
            $this->getRepository(),
            $this->_taskService->getRepository()
        ]), TaskErrorConstant::ERR_TASK_ORDER_APPLY_FAILED);
    }

    /**
     * @param $userId
     * @param Task $task
     * @param bool $apply
     * @return mixed|TaskOrder
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\DBException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    protected function rawCreate($userId, Task $task, $apply = true)
    {
        $attributes = [
            "user_id" => $userId,
            "task_id" => $task->id,
            "task_user_id" => $task->user_id,
            "order_status" => $apply ? TaskOrder::STATUS_NEW : TaskOrder::STATUS_WAITING,
            "order_id" => "",
            "created_at" => time()
        ];
        $taskOrder = $this->getRepository()->create($attributes);
        $this->throwDBException($taskOrder, "创建订单任务失败");

        return $taskOrder;
    }

    public function checkPermission($userId, $taskId)
    {
        $task = $this->_taskService->isValidTask($taskId);
        InternalServiceFactory::getSellerInternalService()->isTaobaoStore($task->store_id)
            ? InternalServiceFactory::getUserInternalService()->isDeployTaobaoAccount($userId)
            : InternalServiceFactory::getUserInternalService()->isDeployJdAccount($userId);

        $this->checkApplyRateByStore($userId, $task->store_id);
        return true;
    }

    protected function checkApplyRateByStore($userId, $storeId)
    {
        $latestTime = (int)TaskBanyanDBConstant::commonTaskOrderUserLatestRecord($userId)->get($storeId);
        time() - $latestTime <= 864000 && ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_MORE_APPLY);
    }

    protected function recordLatestDoneTime($userId, $storeId)
    {
        return TaskBanyanDBConstant::commonTaskOrderUserLatestRecord($userId)->set($storeId, time());
    }

    public function assign($currentUserId, $userId, $taskId)
    {
        $this->checkPermission($userId, $taskId);
        $this->isAllowAssign($userId);

        $taskOrder = $this->doingTransaction(function () use ($currentUserId, $userId, $taskId) {
            $task = $this->_taskService->isMyValidApplyTask($currentUserId, $taskId);
            $taskOrder = $this->rawCreate($userId, $task, false);

            $this->throwDBException(
                $this->_taskService->incWaitingOrder($task),
                "增加任务表订单失败"
            );

            return $taskOrder;
        }, new Collection([
            $this->getRepository(),
            $this->_taskService->getRepository()
        ]), TaskErrorConstant::ERR_TASK_ORDER_ASSIGN_FAILED);

        //todo 发短信通知
        return $taskOrder;
    }

    protected function isAllowAssign($userId)
    {
        InternalServiceFactory::getUserInternalService()->isAutoApplyTask($userId)
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_DISALLOW_ASSIGN_USER);
    }

    public function verify($userId, $taskOrderId)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowVerify($userId, $taskOrder);

        $taskOrder = $this->doingTransaction(function () use ($userId, $taskOrder) {
            $task = $this->_taskService->isValidTask($taskOrder->task_id, true);
            $this->throwDBException(
                $this->getRepository()->verify($taskOrder),
                "审核任务失败"
            );

            $this->throwDBException(
                $this->_taskService->incWaitingOrder($task),
                "增加任务表订单失败"
            );

            return $taskOrder;
        }, new Collection([
            $this->getRepository(),
            $this->_taskService->getRepository()
        ]), TaskErrorConstant::ERR_TASK_ORDER_VERIFY_FAILED);

        //todo 发短信通知
        return $taskOrder;
    }

    protected function isAllowVerify($userId, TaskOrder $taskOrder)
    {
        $this->isAllowDoneOperate($userId, $taskOrder);
        $taskOrder->isNew()
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_DISALLOW_VERIFY);
    }

    public function confirm($userId, $taskOrderId, $storeAccount)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowOperate($userId, $taskOrder);

        $task = $this->_taskService->isValidTask($taskOrder->task_id);
        $store = InternalServiceFactory::getSellerInternalService()->isValidStore($task->store_id);

        $store->store_account == $storeAccount
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_CONFIRM_FAILED);

        return true;
    }

    protected function isAllowOperate($userId, TaskOrder $taskOrder)
    {
        $userId == $taskOrder->user_id
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_OPERATE_ILLEGAL);
    }

    public function doing($userId, $taskOrderId, $orderId, $price)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowDoing($userId, $taskOrder);

        return $this->doingTransaction(function () use ($taskOrder, $orderId, $price) {
            $task = $this->_taskService->isValidTask($taskOrder->task_id, true);
            $price > $task->goods_price && $price = $task->goods_price;//不能多于任务金额

            $this->throwDBException(
                $this->getRepository()->doing($taskOrder, $orderId, $price),
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

    public function sellerConfirm($userId, $taskOrderId)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowSellerConfirm($userId, $taskOrder);

        $result = $this->getRepository()->sellerConfirm($taskOrder);
        $result === false && ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_SELLER_CONFIRM_FAILED);
        return true;
    }

    protected function isAllowSellerConfirm($userId, TaskOrder $taskOrder)
    {
        $this->isAllowDoneOperate($userId, $taskOrder);
        $taskOrder->isDoing()
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_DISALLOW_SELLER_CONFIRM);
    }

    public function buyerConfirm($userId, $taskOrderId)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowBuyerConfirm($userId, $taskOrder);

        $result = $this->getRepository()->buyerConfirm($taskOrder);
        $result === false && ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_BUYER_CONFIRM_FAILED);
        return true;
    }

    protected function isAllowBuyerConfirm($userId, TaskOrder $taskOrder)
    {
        $this->isAllowOperate($userId, $taskOrder);
        $taskOrder->isSellerConfirm()
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_DISALLOW_BUYER_CONFIRM);
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

            //实际金额小于任务金额,剩余金额退款给卖家,佣金根据任务金额来算
            $amount = $this->_taskService->getAmount($task->goods_price);
            $platformCommission = $this->_taskService->getCommission($task->goods_price);
            $buyerCommission = $this->_taskService->getCommission($task->goods_price, 1, false);
            $refundAmount = $task->goods_price - $taskOrder->price;

            InternalServiceFactory::getUserFundInternalService()->pay($userId,
                $amount - $refundAmount,
                $platformCommission,
                "支付任务费用"
            );

            //todo 改成退款
            $this->throwDBException(
                InternalServiceFactory::getUserFundInternalService()->unlock($userId, $refundAmount)
                , "解锁余额失败"
            );


            InternalServiceFactory::getUserFundInternalService()->earn(
                $taskOrder->user_id,
                $taskOrder->price + $buyerCommission,
                "完成任务赚取"
            );

            $this->recordLatestDoneTime($taskOrder->user_id, $task->store_id);

            return true;
        }, new Collection([
            $this->getRepository(),
            $this->_taskService->getRepository()
        ]), TaskErrorConstant::ERR_TASK_ORDER_DONE_FAILED);
    }

    protected function isAllowDone($userId, TaskOrder $taskOrder)
    {
        $this->isAllowDoneOperate($userId, $taskOrder);
        $taskOrder->isBuyerConfirm()
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
                $taskOrder->isTaskWaiting()
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

    public function autoOperate()
    {
        //todo 自动完成 商家确认、买家确认、商家完成
    }

    public function freeze($userId, $taskOrderId)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowFreeze($userId, $taskOrder);

        $result = $this->getRepository()->freeze($taskOrder);
        $result === false && ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_FREEZE_FAILED);
        return true;
    }

    protected function isAllowFreeze($userId, TaskOrder $taskOrder)
    {
        $this->isAllowDoneOperate($userId, $taskOrder);
        $taskOrder->isAllowFreeze()
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_DISALLOW_FREEZE);
    }

    public function unFreeze($adminUserId, $taskOrderId)
    {
        $taskOrder = $this->isValidTaskOrder($taskOrderId);
        $this->isAllowUnFreeze($adminUserId, $taskOrder);

        $result = $this->getRepository()->unFreeze($taskOrder);
        $result === false && ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_UNFREEZE_FAILED);
        return true;
    }

    protected function isAllowUnFreeze($adminUserId, TaskOrder $taskOrder)
    {
        //todo 是否为管理员
        $taskOrder->isFreeze()
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_ORDER_DISALLOW_UNFREEZE);
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