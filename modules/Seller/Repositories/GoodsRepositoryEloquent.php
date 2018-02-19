<?php

namespace Modules\Seller\Repositories;

use Modules\Seller\Models\Goods;
use Modules\Seller\Validators\GoodsValidator;
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
        return GoodsValidator::class;
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
}
