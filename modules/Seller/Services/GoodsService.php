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
    protected $_goodsRepository;

    const BANYAN_SELLER_STAT_GOODS_NUMBER_KEY = "goods_number";

    public function __construct(GoodsRepositoryEloquent $goodsRepositoryEloquent)
    {
        $this->_goodsRepository = $goodsRepositoryEloquent;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function create($userId, $attributes)
    {
        //todo is valid store id

        //todo add price image

        $attributes['user_id'] = $userId;
        $attributes['created_at'] = time();
        $attributes['goods_status'] = GlobalDBConstant::DB_TRUE;

        $goods = $this->_goodsRepository->create($attributes);
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

        $this->decUserGoodsNumber($goods->user_id);

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