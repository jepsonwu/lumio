<?php

namespace Modules\Seller\Services;

use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Account\Services\UserInternalService;
use Modules\Seller\Constants\SellerErrorConstant;
use Modules\Task\Models\Task;
use Modules\Task\Repositories\TaskRepositoryEloquent;
use Modules\UserFund\Services\UserFundInternalService;

class TaskService extends BaseService
{
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
        $task || ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_CREATE_FAILED);

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

    public function list($userId)
    {
        return $this->_taskRepository->getByUserId($userId);
    }

    public function update($userId, $taskId, $totalOrderNumber)
    {

    }

    public function close($id)
    {

    }
}