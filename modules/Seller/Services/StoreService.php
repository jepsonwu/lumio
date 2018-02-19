<?php

namespace Modules\Seller\Services;

use App\Constants\GlobalDBConstant;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Seller\Constants\SellerBanyanDBConstant;
use Modules\Seller\Constants\SellerErrorConstant;
use Modules\Seller\Models\Store;
use Modules\Seller\Repositories\StoreRepositoryEloquent;

class StoreService extends BaseService
{
    protected $_storeRepository;

    const MAX_STORE_NUMBER = 10;

    const BANYAN_SELLER_STAT_STORE_NUMBER_KEY = "store_number";

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

        return $store;
    }

    protected function isAllowCreate($userId)
    {
        $this->getUserStoreNumber($userId) >= self::MAX_STORE_NUMBER
        && ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_DISALLOW_CREATE);
    }

    protected function getUserStoreNumber($userId)
    {
        return (int)SellerBanyanDBConstant::commonSellerStat($userId)->get(self::BANYAN_SELLER_STAT_STORE_NUMBER_KEY);
    }

    protected function incUserStoreNumber($userId)
    {
        SellerBanyanDBConstant::commonSellerStat($userId)->inc(self::BANYAN_SELLER_STAT_STORE_NUMBER_KEY);
    }

    protected function decUserStoreNumber($userId)
    {
        SellerBanyanDBConstant::commonSellerStat($userId)->inc(self::BANYAN_SELLER_STAT_STORE_NUMBER_KEY, -1);
    }

    public function checkDeployStore($userId)
    {
        $this->getUserStoreNumber($userId) < 1
        && ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_NO_DEPLOY);
    }

    public function pass($id)
    {
        $store = $this->isValidStore($id);
        $this->isAllowVerify($store);

        $result = $this->_storeRepository->pass($store);
        $result || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_VERIFY_FAILED);

        $this->incUserStoreNumber($store->user_id);

        return true;
    }

    protected function isAllowVerify(Store $store)
    {
        $store->isWaitingVerify()
        || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_VERIFIED);
    }

    public function fail($id)
    {
        $store = $this->isValidStore($id);
        $this->isAllowVerify($store);

        $result = $this->_storeRepository->fail($store);
        $result || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_VERIFY_FAILED);

        return true;
    }

    public function list($userId)
    {
        return $this->_storeRepository->getByUserId($userId);
    }

    public function update($userId, $storeId, $attributes)
    {
        $store = $this->isValidStore($storeId);
        $this->isAllowUpdate($userId, $store);
        $store = $this->_storeRepository->update($attributes, $storeId);
        $store || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_UPDATE_FAILED);

        return $store;
    }

    protected function isAllowUpdate($userId, Store $store)
    {
        $store->isWaitingVerify()
        || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_DISALLOW_UPDATE);

        $this->isAllowOperate($userId, $store);
    }

    protected function isAllowOperate($userId, Store $store)
    {
        $userId == $store->user_id
        || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_OPERATE_ILLEGAL);
    }

    public function delete($userId, $storeId)
    {
        $store = $this->isValidStore($storeId);
        $this->isAllowDelete($userId, $store);
        $result = $this->_storeRepository->deleteStore($store);
        $result || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_DELETE_FAILED);

        //todo 权限

        //todo 事物 删除商品

        $this->decUserStoreNumber($store->user_id);

        return true;
    }

    protected function isAllowDelete($userId, Store $store)
    {
        //todo 当前有任务
        $this->isAllowOperate($userId, $store);
    }

    public function isValidStore($id)
    {
        /**@var Store $store * */
        $store = $this->_storeRepository->find($id);
        (!$store || !$store->isValid())
        && ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_INVALID);

        return $store;
    }
}