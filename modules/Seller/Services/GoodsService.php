<?php

namespace Modules\Seller\Services;

use App\Components\Factories\InternalServiceFactory;
use App\Constants\GlobalDBConstant;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Seller\Constants\SellerErrorConstant;
use Modules\Seller\Models\Goods;
use Modules\Seller\Repositories\GoodsRepositoryEloquent;
use Modules\Seller\Constants\SellerBanyanDBConstant;
use Illuminate\Support\Collection;

class GoodsService extends BaseService
{
    protected $_storeService;

    const BANYAN_SELLER_STAT_GOODS_NUMBER_KEY = "goods_number";

    public function __construct(
        GoodsRepositoryEloquent $goodsRepositoryEloquent
    )
    {
        $this->setRepository($goodsRepositoryEloquent);
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function create($userId, $attributes)
    {
        $this->getStoreService()->isMyAvailableStore($attributes['store_id'], $userId);

        //todo 重名判断

        //todo add price image

        $attributes['user_id'] = $userId;
        $attributes['goods_image'] = '';
        $attributes['goods_price'] = 0;
        $attributes['created_at'] = time();
        $attributes['goods_status'] = GlobalDBConstant::DB_TRUE;

        $goods = $this->getRepository()->create($attributes);
        $goods || ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_CREATE_FAILED);

        $this->incUserGoodsNumber($userId);

        return $goods;
    }

    protected function getUserGoodsNumber($userId)
    {
        return (int)SellerBanyanDBConstant::commonSellerStat($userId)->get(self::BANYAN_SELLER_STAT_GOODS_NUMBER_KEY);
    }

    protected function incUserGoodsNumber($userId)
    {
        SellerBanyanDBConstant::commonSellerStat($userId)->inc(self::BANYAN_SELLER_STAT_GOODS_NUMBER_KEY);
    }

    protected function decUserGoodsNumber($userId)
    {
        SellerBanyanDBConstant::commonSellerStat($userId)->inc(self::BANYAN_SELLER_STAT_GOODS_NUMBER_KEY, -1);
    }

    public function checkDeployGoods($userId)
    {
        $this->getUserGoodsNumber($userId) < 1
        && ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_NO_DEPLOY);
    }

    public function list($conditions)
    {
        return $this->getRepository()->paginateWithWhere($conditions, 10);
    }

    public function update($userId, $goodsId, $attributes)
    {
        $goods = $this->isValidGoods($goodsId);
        $this->isAllowUpdate($userId, $goods);

        //todo add price image

        $goods = $this->getRepository()->update($attributes, $goodsId);
        $goods || ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_UPDATE_FAILED);

        return $goods;
    }

    protected function isAllowUpdate($userId, Goods $goods)
    {
        $this->isAllowOperate($userId, $goods);
    }

    protected function isAllowOperate($userId, Goods $goods)
    {
        $userId == $goods->user_id
        || ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_OPERATE_ILLEGAL);
    }

    public function delete($userId, $goodsId)
    {
        $goods = $this->isValidGoods($goodsId);
        $this->isAllowDelete($userId, $goods);


        $this->doingTransaction(function () use ($goods) {
            $result = $this->getRepository()->deleteGoods($goods);
            $this->throwDBException($result, "delete goods failed");

            $this->throwDBException(
                $this->decUserGoodsNumber($goods->user_id),
                "dec user goods number failed"
            );
        }, new Collection([
            $this->getRepository()
        ]), SellerErrorConstant::ERR_GOODS_DELETE_FAILED);

        return true;
    }

    protected function isAllowDelete($userId, Goods $goods)
    {
        $this->isAllowOperate($userId, $goods);

        InternalServiceFactory::getTaskInternalService()->checkActiveByGoods($goods->id)
        && ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_DISALLOW_DELETE);
    }

    public function deleteByStoreId($storeId)
    {
        return $this->getRepository()->deleteByStoreId($storeId);
    }

    public function isValidGoods($id)
    {
        /**@var Goods $goods * */
        $goods = $this->getRepository()->find($id);
        (!$goods || !$goods->isValid())
        && ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_INVALID);

        return $goods;
    }

    public function isValidMyGoods($userId, $goodsId)
    {
        $goods = $this->isValidGoods($goodsId);
        $this->isAllowOperate($userId, $goods);

        return $goods;
    }

    /**
     * @return mixed|GoodsRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }

    /**
     * @return StoreService
     */
    protected function getStoreService()
    {
        is_null($this->_storeService) && $this->_storeService = app(StoreService::class);
        return $this->_storeService;
    }
}