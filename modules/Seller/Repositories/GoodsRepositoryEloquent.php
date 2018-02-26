<?php

namespace Modules\Seller\Repositories;

use App\Constants\GlobalDBConstant;
use App\Validators\GlobalValidator;
use Modules\Seller\Models\Goods;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class GoodsRepositoryEloquent
 * @package namespace Modules\Seller\Repositories;
 */
class GoodsRepositoryEloquent extends BaseRepository
{
    /**
     * @var Goods
     */
    protected $model;

    public function model()
    {
        return Goods::class;
    }

    public function validator()
    {
        return GlobalValidator::class;
    }

    /**
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Collection|static[]|
     */
    public function getByUserId($userId)
    {
        $builder = $this->model->newQuery();
        $this->model->whereUserId($builder, $userId)->whereValid($builder);
        return $builder->get();
    }

    public function deleteGoods(Goods $goods)
    {
        return $goods->deleteGoods();
    }

    public function deleteByStoreId($storeId)
    {
        $builder = $this->model->newQuery();
        return $this->model->whereStoreId($builder, $storeId)->update([
            "goods_status" => GlobalDBConstant::DB_FALSE
        ]);
    }
}
