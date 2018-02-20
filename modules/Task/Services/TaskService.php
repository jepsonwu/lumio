<?php

namespace Modules\Seller\Services;

use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Jiuyan\Common\Component\InFramework\Traits\DBTrait;
use Modules\Account\Services\UserInternalService;
use Modules\Seller\Models\Goods;
use Modules\Task\Constants\TaskErrorConstant;
use Modules\Task\Models\Task;
use Modules\Task\Repositories\TaskRepositoryEloquent;
use Modules\UserFund\Services\UserFundInternalService;

class TaskService extends BaseService
{
    use DBTrait;

    protected $_taskRepository;
    protected $_sellerInternalService;//todo implements
    protected $_userInternalService;
    protected $_userFundInternalService;

    public function __construct(
        TaskRepositoryEloquent $taskRepositoryEloquent,
        SellerInternalService $sellerInternalService,
        UserInternalService $userInternalService,
        UserFundInternalService $userFundInternalService
    )
    {
        $this->_taskRepository = $taskRepositoryEloquent;
        $this->_sellerInternalService = $sellerInternalService;
        $this->_userInternalService = $userInternalService;
        $this->_userFundInternalService = $userFundInternalService;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function create($userId, $attributes)
    {
        $this->isAllowCreate($userId);
        $goods = $this->_sellerInternalService->isValidMyGoods($userId, $attributes['goods_id']);

        $amount = $goods->goods_price * $attributes['total_order_number'];
        $this->checkBalance($userId, $amount);

//        $this->doingTransaction(function (){
//
//        },);
        $task = $this->rawCreate($userId, $goods);

        //锁定余额
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
        $attributes['task_status'] = Task::STATUS_WAITING;
        $attributes['created_at'] = time();

        $task = $this->_taskRepository->create($attributes);
        $task || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_CREATE_FAILED);

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

    }

    public function list($userId)
    {
        return $this->_taskRepository->getByUserId($userId);
    }

    public function update($userId, $taskId, $totalOrderNumber)
    {
        $task = $this->isValidTask($taskId);
        $this->isAllowUpdate($userId, $task);

        $attributes['total_order_number'] = $totalOrderNumber;
        $task = $this->_taskRepository->update($attributes, $taskId);
        $task || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_UPDATE_FAILED);

        return $task;
    }

    public function isValidTask($taskId)
    {
        /**@var Task $task * */
        $task = $this->_taskRepository->find($taskId);
        $task || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_INVALID);

        return $task;
    }

    protected function isAllowUpdate($userId, Task $task)
    {
        $this->isAllowOperate($userId, $task);
    }

    protected function isAllowOperate($userId, Task $task)
    {
        $userId == $task->user_id
        || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_OPERATE_ILLEGAL);
    }

    public function close($userId, $taskId)
    {
        $task = $this->isValidTask($taskId);
        $this->isAllowClose($userId, $task);

        $result = $this->_taskRepository->closeTask($task);
        $result || ExceptionResponseComponent::business(TaskErrorConstant::ERR_TASK_CLOSE_FAILED);

        return true;
    }

    protected function isAllowClose($userId, Task $task)
    {
        $this->isAllowOperate($userId, $task);
    }
}