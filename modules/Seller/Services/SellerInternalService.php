<?php

namespace Modules\Seller\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;

class SellerInternalService extends BaseService
{
    protected $goodsService;
    protected $storeService;

    public function __construct(StoreService $storeService, GoodsService $goodsService)
    {
        $this->storeService = $storeService;
        $this->goodsService = $goodsService;
    }

    public function isTaobaoStore($storeId)
    {
        $store = $this->storeService->isValidStore($storeId);
        return $this->storeService->isTaobao($store);
    }

    public function isJdStore($storeId)
    {
        $store = $this->storeService->isValidStore($storeId);
        return $this->storeService->isJd($store);
    }

    public function isValidStore($storeId)
    {
        return $this->storeService->isValidStore($storeId);
    }

    public function isValidGoods($goodsId)
    {
        return $this->goodsService->isValidGoods($goodsId);
    }

    public function isValidMyGoods($userId, $goodsId)
    {
        return $this->goodsService->isValidMyGoods($userId, $goodsId);
    }

    public function isFinishedDeploy($userId)
    {
        $this->storeService->checkDeployStore($userId);

        $this->goodsService->checkDeployGoods($userId);
    }
}