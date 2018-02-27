<?php

namespace Modules\Seller\Services;

use App\Constants\GlobalDBConstant;
use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Seller\Constants\SellerBanyanDBConstant;
use Modules\Seller\Constants\SellerErrorConstant;
use Modules\Seller\Models\Store;
use Modules\Seller\Repositories\StoreRepositoryEloquent;
use Modules\Task\Services\TaskInternalService;

class StoreService extends BaseService
{
    const MAX_STORE_NUMBER = 10;

    const BANYAN_SELLER_STAT_STORE_NUMBER_KEY = "store_number";

    protected $_goodService;
    protected $_taskInternalService;

    public function __construct(
        StoreRepositoryEloquent $storeRepositoryEloquent,
        TaskInternalService $taskInternalService
    )
    {
        $this->setRepository($storeRepositoryEloquent);
        $this->_taskInternalService = $taskInternalService;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function create($userId, $attributes)
    {
        $this->isAllowCreate($userId);

        $attributes['user_id'] = $userId;
        $attributes['created_at'] = time();
        $attributes['verify_status'] = Store::VERIFY_STATUS_WAITING;
        $attributes['store_status'] = GlobalDBConstant::DB_TRUE;

        $store = $this->getRepository()->create($attributes);
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

        $result = $this->getRepository()->pass($store);
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

        $result = $this->getRepository()->fail($store);
        $result || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_VERIFY_FAILED);

        return true;
    }

    public function list($userId)
    {
        return $this->getRepository()->getByUserId($userId);
    }

    public function update($userId, $storeId, $attributes)
    {
        $store = $this->isValidStore($storeId);
        $this->isAllowUpdate($userId, $store);
        $store = $this->getRepository()->update($attributes, $storeId);
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

        $this->doingTransaction(function () use ($store) {
            $result = $this->getRepository()->deleteStore($store);
            $this->throwDBException($result, "delete store failed");

            $this->throwDBException(
                $this->getGoodsService()->deleteByStoreId($store->id),
                "delete goods by store failed"
            );

            $store->isPassed() && $this->throwDBException(
                $this->decUserStoreNumber($store->user_id),
                "dec user store number failed"
            );
        }, new Collection([
            $this->getRepository(),
            $this->getGoodsService()->getRepository()
        ]), SellerErrorConstant::ERR_STORE_DELETE_FAILED);

        return true;
    }

    protected function isAllowDelete($userId, Store $store)
    {
        $this->isAllowOperate($userId, $store);
        $this->_taskInternalService->checkActiveByStore($store->id)
        && ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_DISALLOW_DELETE);
    }

    public function isValidStore($id)
    {
        /**@var Store $store * */
        $store = $this->getRepository()->find($id);
        (!$store || !$store->isValid())
        && ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_INVALID);

        return $store;
    }

    public function isValidMyStore($storeId, $userId)
    {
        $store = $this->isValidStore($storeId);
        $this->isAllowOperate($userId, $store);

        return $store;
    }

    public function isMyAvailableStore($storeId, $userId)
    {
        $store = $this->isValidMyStore($storeId, $userId);
        $store->isPassed()
        || ExceptionResponseComponent::business(SellerErrorConstant::ERR_STORE_NO_PASSED);

        return $store;
    }

    public function isTaobao(Store $store)
    {
        return $store->isTaobao();
    }

    public function isJd(Store $store)
    {
        return $store->isJd();
    }

    /**
     * @return mixed|StoreRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }

    /**
     * @return GoodsService
     */
    protected function getGoodsService()
    {
        is_null($this->_goodService) && $this->_goodService = app(GoodsService::class);
        return $this->_goodService;
    }
}