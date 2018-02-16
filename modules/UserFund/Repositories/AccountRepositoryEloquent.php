<?php

namespace Modules\UserFund\Repositories;

use Modules\UserFund\Models\Account;
use Modules\UserFund\Validators\AccountValidator;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class AccountRepositoryEloquent
 * @package namespace Modules\Account\Repositories;
 */
class AccountRepositoryEloquent extends BaseRepository
{
    /**
     * @var Account
     */
    protected $model;

    public function model()
    {
        return Account::class;
    }

    public function validator()
    {
        return AccountValidator::class;
    }

    /**
     * @param $bankCard
     * @return \Illuminate\Database\Eloquent\Model|null|static|Account
     */
    public function getByBankCard($bankCard)
    {
        $builder = $this->model->newQuery();
        $this->model->whereBankCard($builder, $bankCard)->whereValid($builder);
        return $builder->first();
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
}
