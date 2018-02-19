<?php

namespace Modules\Seller\Services;

use App\Constants\GlobalDBConstant;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Seller\Constants\SellerErrorConstant;
use Modules\Seller\Models\Store;
use Modules\Seller\Repositories\StoreRepositoryEloquent;

class StoreService extends BaseService
{
    protected $_storeRepository;

    public function __construct(StoreRepositoryEloquent $storeRepositoryEloquent)
    {
        $this->_storeRepository = $storeRepositoryEloquent;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function create($userId, $attributes)
    {
        $this->isAllowCreate($userId);

        $attributes['user_id'] = $userId;
        $attributes['created_at'] = time();
        $attributes['verify_status'] = Store::VERIFY_STATUS_WAITING;
        $attributes['store_status'] = GlobalDBConstant::DB_TRUE;

        $store = $this->_storeRepository->create($attributes);
        $store || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_CREATE_FAILED);

        //todo 权限判断

        return $store;
    }

    protected function isAllowCreate($userId)
    {
        //todo 最多添加10
    }

    public function list($userId)
    {
        return $this->_storeRepository->getByUserId($userId);
    }

    public function update($id, $attributes)
    {
        $store = $this->isValidStore($id);
        $this->isAllowUpdate($store);
        $store = $this->_storeRepository->update($attributes, $id);
        $store || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_UPDATE_FAILED);

        return $store;
    }

    public function delete($id)
    {
        $store = $this->isValidStore($id);
        $result = $this->_storeRepository->deleteStore($store);
        $result || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_DELETE_FAILED);

        //todo 权限

        //todo 事物 删除商品

        return true;
    }

    public function isValidStore($id)
    {
        /**@var Store $store * */
        $store = $this->_storeRepository->find($id);
        (!$store || !$store->isValid())
        && ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_INVALID);

        return $store;
    }

    protected function isAllowUpdate(Store $store)
    {
        $store->isWaitingVerify()
        || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_DISALLOW_UPDATE);
    }

    protected function isAllowDelete()
    {
        //todo 当前有任务
    }
}