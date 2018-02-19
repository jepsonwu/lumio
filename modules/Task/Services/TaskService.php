<?php

namespace Modules\Seller\Services;

use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Seller\Constants\SellerErrorConstant;
use Modules\Seller\Models\Goods;
use Modules\Task\Models\Task;
use Modules\Task\Repositories\TaskRepositoryEloquent;

class TaskService extends BaseService
{
    protected $_taskRepository;
    protected $_sellerInternalService;//todo implements

    public function __construct(
        TaskRepositoryEloquent $taskRepositoryEloquent,
        SellerInternalService $sellerInternalService
    )
    {
        $this->_taskRepository = $taskRepositoryEloquent;
        $this->_sellerInternalService = $sellerInternalService;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function create($userId, $attributes)
    {
        $this->isAllowCreate($userId);

        $goods = $this->_sellerInternalService->isValidGoods($attributes['goods_id']);
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
        //todo is seller

        $this->_sellerInternalService->isFinishedDeploy($userId);
    }

    public function list($userId)
    {
        return $this->_goodsRepository->getByUserId($userId);
    }

    public function update($id, $attributes)
    {
        $this->isValidGoods($id);
        $goods = $this->_goodsRepository->update($attributes, $id);
        $goods || ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_UPDATE_FAILED);

        return $goods;
    }

    public function delete($id)
    {
        $goods = $this->isValidGoods($id);
        $result = $this->_goodsRepository->deleteGoods($goods);
        $result || ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_DELETE_FAILED);

        //todo 权限

        //todo 事物 删除商品

        return true;
    }

    public function isValidGoods($id)
    {
        /**@var Goods $goods * */
        $goods = $this->_goodsRepository->find($id);
        (!$goods || !$goods->isValid())
        && ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_INVALID);

        return $goods;
    }

    protected function isAllowDelete()
    {
        //todo 当前有任务
    }
}