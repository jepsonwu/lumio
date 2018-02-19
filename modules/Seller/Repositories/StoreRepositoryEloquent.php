<?php

namespace Modules\Seller\Repositories;

use Modules\Seller\Models\Store;
use Modules\Seller\Validators\StoreValidator;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class StoreRepositoryEloquent
 * @package namespace Modules\Seller\Repositories;
 */
class StoreRepositoryEloquent extends BaseRepository
{
    /**
     * @var Store
     */
    protected $model;

    public function model()
    {
        return Store::class;
    }

    public function validator()
    {
        return StoreValidator::class;
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

    public function deleteStore(Store $store)
    {
        return $store->deleteStore();
    }

    public function pass(Store $store)
    {
        return $store->pass();
    }

    public function fail(Store $store)
    {
        return $store->fail();
    }
}
