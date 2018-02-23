<?php

namespace Modules\Seller\Services;

use App\Constants\GlobalDBConstant;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Seller\Constants\SellerErrorConstant;
use Modules\Seller\Models\Goods;
use Modules\Seller\Repositories\GoodsRepositoryEloquent;
use Modules\Seller\Constants\SellerBanyanDBConstant;

class GoodsService extends BaseService
{
    protected $_storeService;

    const BANYAN_SELLER_STAT_GOODS_NUMBER_KEY = "goods_number";

    public function __construct(
        GoodsRepositoryEloquent $goodsRepositoryEloquent,
        StoreService $storeService
    )
    {
        $this->setRepository($goodsRepositoryEloquent);
        $this->_storeService = $storeService;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function create($userId, $attributes)
    {
        $this->_storeService->isValidMyStore($attributes['store_id'], $userId);

        //todo add price image

        $attributes['user_id'] = $userId;
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

    public function list($userId)
    {
        return $this->getRepository()->getByUserId($userId);
    }

    public function update($userId, $goodsId, $attributes)
    {
        $goods = $this->isValidGoods($goodsId);
        $this->isAllowUpdate($userId, $goods);

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

        $result = $this->getRepository()->deleteGoods($goods);
        $result || ExceptionResponseComponent::business(SellerErrorConstant::ERR_GOODS_DELETE_FAILED);

        //todo 权限

        //todo 事物 删除商品

        $this->decUserGoodsNumber($goods->user_id);

        return true;
    }

    protected function isAllowDelete($userId, Goods $goods)
    {
        //todo 当前有任务
        $this->isAllowOperate($userId, $goods);
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
}