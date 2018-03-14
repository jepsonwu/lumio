<?php

namespace Modules\Seller\Repositories;

use App\Validators\GlobalValidator;
use Modules\Seller\Models\Store;
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

    public function deleteStore(Store $store)
    {
        return $store->deleteStore();
    }

    public function pass(Store $store, $verifyUserId)
    {
        return $store->pass($verifyUserId);
    }

    public function fail(Store $store, $reason, $verifyUserId)
    {
        return $store->fail($reason, $verifyUserId);
    }

    public function isTaobao(Store $store)
    {
        return $store->isTaobao();
    }

    public function isJd(Store $store)
    {
        return $store->isJd();
    }
}
