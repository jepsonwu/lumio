<?php

namespace Modules\Seller\Services;

use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Account\Services\UserInternalService;
use Modules\Seller\Models\Goods;
use Modules\Task\Constants\TaskErrorConstant;
use Modules\Task\Models\Task;
use Modules\Task\Repositories\TaskRepositoryEloquent;
use Modules\UserFund\Services\UserFundInternalService;

class TaskService extends BaseService
{
    protected $_sellerInternalService;
    protected $_userInternalService;
    protected $_userFundInternalService;

    public function __construct(
        TaskRepositoryEloquent $taskRepositoryEloquent,
        SellerInternalService $sellerInternalService,
        UserInternalService $userInternalService,
        UserFundInternalService $userFundInternalService
    )
    {
        $this->setRepository($taskRepositoryEloquent);
        $this->_sellerInternalService = $sellerInternalService;
        $this->_userInternalService = $userInternalService;
        $this->_userFundInternalService = $userFundInternalService;
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
        $goods = $this->_sellerInternalService->isValidMyGoods($userId, $attributes['goods_id']);

        $amount = $goods->goods_price * $attributes['total_order_number'];
        $this->checkBalance($userId, $amount);

        $task = $this->doingTransaction(function () use ($userId, $goods, $amount) {
            $task = $this->rawCreate($userId, $goods);

            $this->throwDBException($this->_userFundInternalService->lock($userId, $amount), "锁定余额失败");
            return $task;
        }, new Collection(
            array_merge([$this->getRepository()], $this->_userFundInternalService->getRepository())
        ), TaskErrorConstant::ERR_TASK_CREATE_FAILED);

        return $task;
    }

    protected function rawCreate($userId, Goods $goods)
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
        if (!$this->_userInternalService->isSeller($userId)) {
            $this->_userFundInternalService->isFinishedDeployAccount($userId);
            $this->_userFundInternalService->isFinishedDeployWallet($userId);//todo optimize
        }

        $this->_sellerInternalService->isFinishedDeploy($userId);
    }

    protected function checkBalance($userId, $amount)
    {
        return $this->_userFundInternalService->checkBalance($userId, $amount);
    }

    public function list($userId)
    {
        return $this->getRepository()->getByUserId($userId);
    }


    /**
     * 1.只能修改总订单数、搜索关键字 增加考虑余额 减少考虑当前正在执行的任务
     * 2.修改数量
     * 3.锁定、解锁余额
     * @param $userId
     * @param $taskId
     * @param $totalOrderNumber
     * @return Task
     * @throws \Exception
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function update($userId, $taskId, $totalOrderNumber)
    {
        $task = $this->isValidTask($taskId);
        if ($task->total_order_number == $totalOrderNumber) {
            return $task;
        }

        $incOrderNumber = $totalOrderNumber - $task->total_order_number;
        $this->isAllowUpdate($userId, $task, $incOrderNumber);

        $this->doingTransaction(function () use ($userId, $task, $incOrderNumber) {
            $this->rawUpdate($task->id, $incOrderNumber + $task->total_order_number);

            $amount = $task->goods_price * $incOrderNumber;
            if ($incOrderNumber > 0) {
                $result = $this->_userFundInternalService->lock($userId, $amount);
            } else {
                $result = $this->_userFundInternalService->unlock($userId, -$amount);;
            }

            $this->throwDBException($result, "修改操作余额失败");
        }, new Collection(
            array_merge([$this->getRepository()], $this->_userFundInternalService->getRepository())
        ), TaskErrorConstant::ERR_TASK_UPDATE_FAILED);


        return $task;
    }

    protected function rawUpdate($taskId, $totalOrderNumber)
    {
        $attributes['total_order_number'] = $totalOrderNumber;
        $task = $this->getRepository()->update($attributes, $taskId);
        $this->throwDBException($task, "修改失败");
    }

    public function isValidTask($taskId)
    {
        /**@var Task $task * */
        $task = $this->getRepository()->find($taskId);
        $task || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_INVALID);

        return $task;
    }

    protected function isAllowUpdate($userId, Task $task, $incOrderNumber)
    {
        $this->isAllowOperate($userId, $task);

        if (!($task->isWaiting() || $task->isDoing())) {
            ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_DISALLOW_UPDATE);
        }

        if ($incOrderNumber > 0) {
            $this->checkBalance($userId, $task->goods_price * $incOrderNumber);
        } else {
            if (($task->waiting_order_number + $task->doing_order_number) > $incOrderNumber) {
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
                $this->_userFundInternalService->unlock($userId, $this->getRefundAmount($task))
                , "解锁余额失败"
            );
        }, new Collection(
            array_merge([$this->getRepository()], $this->_userFundInternalService->getRepository())
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

    public function incWaitingOrder(Task $task)
    {
        return $this->getRepository()->incWaitingOrder($task);
    }

    public function incDoingOrder(Task $task)
    {
        return $this->getRepository()->incDoingOrder($task);
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