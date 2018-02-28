<?php

namespace Modules\Task\Services;

use App\Components\Factories\InternalServiceFactory;
use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Seller\Models\Goods;
use Modules\Task\Constants\TaskErrorConstant;
use Modules\Task\Models\Task;
use Modules\Task\Repositories\TaskRepositoryEloquent;

class TaskService extends BaseService
{

    public function __construct(
        TaskRepositoryEloquent $taskRepositoryEloquent
    )
    {
        $this->setRepository($taskRepositoryEloquent);
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    /**
     * 1.校验余额
     * 2.创建任务
     * 3.锁定账户金额
     * 4.todo 如果要完全分模块 需要考虑分布式事务
     *
     * @param $userId
     * @param $attributes
     * @return mixed
     * @throws \Exception
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function create($userId, $attributes)
    {
        $this->isAllowCreate($userId);
        $goods = InternalServiceFactory::getSellerInternalService()->isValidMyGoods(
            $userId,
            $attributes['goods_id']
        );

        $amount = $goods->goods_price * $attributes['total_order_number'];
        $this->checkBalance($userId, $amount);

        $task = $this->doingTransaction(function () use ($userId, $goods, $amount, $attributes) {
            $task = $this->rawCreate($attributes, $userId, $goods);

            $this->throwDBException(
                InternalServiceFactory::getUserFundInternalService()->lock($userId, $amount),
                "锁定余额失败"
            );
            return $task;
        }, new Collection(
            array_merge(
                [$this->getRepository()],
                InternalServiceFactory::getUserFundInternalService()->getRepository()
            )
        ), TaskErrorConstant::ERR_TASK_CREATE_FAILED);

        return $task;
    }

    protected function rawCreate($attributes, $userId, Goods $goods)
    {
        $attributes['user_id'] = $userId;
        $attributes['store_id'] = $goods->store_id;
        $attributes['goods_id'] = $goods->id;
        $attributes['goods_name'] = $goods->goods_name;
        $attributes['goods_price'] = $goods->goods_price;
        $attributes['goods_image'] = $goods->goods_image;
        $attributes['finished_order_number'] = 0;
        $attributes['doing_order_number'] = 0;
        $attributes['waiting_order_number'] = 0;
        $attributes['task_status'] = Task::STATUS_WAITING;
        $attributes['created_at'] = time();

        $task = $this->getRepository()->create($attributes);
        $this->throwDBException($task, "创建任务失败");

        return $task;
    }

    public function isAllowCreate($userId)
    {
        if (!InternalServiceFactory::getUserInternalService()->isSeller($userId)) {
            //InternalServiceFactory::getUserFundInternalService()->isFinishedDeployAccount($userId);
            InternalServiceFactory::getUserFundInternalService()->isFinishedDeployWallet($userId);
        }

        InternalServiceFactory::getSellerInternalService()->isFinishedDeploy($userId);
    }

    protected function checkBalance($userId, $amount)
    {
        return InternalServiceFactory::getUserFundInternalService()->checkBalance($userId, $amount);
    }

    public function list($conditions)
    {
        return $this->getRepository()->paginateWithWhere($conditions, 10);
    }

    /**
     * 1.只能修改总订单数、搜索关键字、平台 增加考虑余额 减少考虑当前正在执行的任务
     * 2.修改数量
     * 3.锁定、解锁余额
     * @param $userId
     * @param $taskId
     * @param $attributes
     * @return Task
     * @throws \Exception
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function update($userId, $taskId, $attributes)
    {
        $task = $this->isValidTask($taskId);
        $this->isAllowUpdate($userId, $task);

        $incOrderNumber = $attributes['total_order_number'] - $task->total_order_number;
        $incOrderNumber !== 0 && $this->isAllowUpdateTotalNumber($userId, $task, $incOrderNumber);

        return $this->doingTransaction(function () use ($userId, $task, $incOrderNumber, $attributes) {
            $attributes['total_order_number'] = $incOrderNumber + $task->total_order_number;
            $task = $this->rawUpdate($task->id, $attributes);

            if ($incOrderNumber !== 0) {
                $amount = $task->goods_price * $incOrderNumber;
                if ($incOrderNumber > 0) {
                    $result = InternalServiceFactory::getUserFundInternalService()->lock($userId, $amount);
                } else {
                    $result = InternalServiceFactory::getUserFundInternalService()->unlock($userId, -$amount);;
                }

                $this->throwDBException($result, "修改操作余额失败");
            }

            return $task;
        }, new Collection(
            array_merge(
                [$this->getRepository()],
                InternalServiceFactory::getUserFundInternalService()->getRepository()
            )
        ), TaskErrorConstant::ERR_TASK_UPDATE_FAILED);
    }

    protected function rawUpdate($taskId, $attributes)
    {
        $task = $this->getRepository()->update($attributes, $taskId);
        $this->throwDBException($task, "修改失败");
        return $task;
    }

    /**
     * @param $taskId
     * @param bool $forUpdate
     * @return Collection|mixed|\Prettus\Repository\Database\Eloquent\Model|Task
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function isValidTask($taskId, $forUpdate = false)
    {
        $task = $forUpdate
            ? $this->getRepository()->findForUpdate($taskId)
            : $this->getRepository()->find($taskId);
        $task || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_INVALID);

        return $task;
    }

    public function isValidApplyTask($taskId)
    {
        $task = $this->isValidTask($taskId, true);
        $task->isAllowApply()
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_DISALLOW_APPLY);

        return $task;
    }

    public function isMyValidApplyTask($userId, $taskId)
    {
        $task = $this->isValidApplyTask($taskId);
        $this->isAllowOperate($userId, $task);

        return $task;
    }

    protected function isAllowUpdate($userId, Task $task)
    {
        $this->isAllowOperate($userId, $task);

        if (!($task->isWaiting() || $task->isDoing())) {
            ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_DISALLOW_UPDATE);
        }
    }

    protected function isAllowUpdateTotalNumber($userId, Task $task, $incOrderNumber)
    {
        if ($incOrderNumber > 0) {
            $this->checkBalance($userId, $task->goods_price * $incOrderNumber);
        } else {
            $availableDec = $task->total_order_number - (($task->finished_order_number + $task->waiting_order_number + $task->doing_order_number));
            if ($availableDec < -$incOrderNumber) {
                ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_DISALLOW_UPDATE);
            }
        }
    }

    protected function isAllowOperate($userId, Task $task)
    {
        $userId == $task->user_id
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_OPERATE_ILLEGAL);
    }

    /**
     * 1.只能删除 等待中、执行中 并且没有未完成的执行任务
     * 2.删除任务
     * 3.退款
     * 4.todo 脚本执行，关闭过期任务
     *
     * @param $userId
     * @param $taskId
     * @return bool
     * @throws \Exception
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function close($userId, $taskId)
    {
        $task = $this->isValidTask($taskId);
        $this->isAllowClose($userId, $task);

        $this->doingTransaction(function () use ($userId, $task) {
            $this->rawClose($task);

            $this->throwDBException(
                InternalServiceFactory::getUserFundInternalService()->unlock($userId, $this->getRefundAmount($task))
                , "解锁余额失败"
            );
        }, new Collection(
            array_merge(
                [$this->getRepository()],
                InternalServiceFactory::getUserFundInternalService()->getRepository()
            )
        ), TaskErrorConstant::ERR_TASK_CLOSE_FAILED);

        return true;
    }

    protected function getRefundAmount(Task $task)
    {
        return $task->goods_price * ($task->total_order_number - $task->finished_order_number);
    }

    protected function rawClose(Task $task)
    {
        $result = $this->getRepository()->closeTask($task);
        $this->throwDBException($result, "关闭任务失败");
    }

    protected function isAllowClose($userId, Task $task)
    {
        $this->isAllowOperate($userId, $task);

        if (!(
            ($task->isWaiting() || $task->isDoing())
            && $task->waiting_order_number < 1
            && $task->doing_order_number < 1
        )) {
            ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_DISALLOW_CLOSE);
        }
    }

    public function checkActiveByGoods($goodsId)
    {
        return $this->getRepository()->checkActiveByGoods($goodsId);
    }

    public function checkActiveByStore($storeId)
    {
        return $this->getRepository()->checkActiveByStore($storeId);
    }

    public function incWaitingOrder(Task $task)
    {
        return $this->getRepository()->incWaitingOrder($task);
    }

    public function decWaitingOrder(Task $task)
    {
        return $this->getRepository()->decWaitingOrder($task);
    }

    public function incDoingOrder(Task $task)
    {
        return $this->getRepository()->incDoingOrder($task);
    }

    public function decDoingOrder(Task $task)
    {
        return $this->getRepository()->decDoingOrder($task);
    }

    public function incDoneOrder(Task $task)
    {
        return $this->getRepository()->incFinishedOrder($task);
    }

    /**
     * @return mixed|TaskRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }
}