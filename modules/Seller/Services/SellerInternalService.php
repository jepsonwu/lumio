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

    public function isValidGoods($goodsId)
    {
        return $this->goodsService->isValidGoods($goodsId);
    }

    public function isFinishedDeploy($userId)
    {
        $this->storeService->checkDeployStore($userId);

        $this->goodsService->checkDeployGoods($userId);
    }
}