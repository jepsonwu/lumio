<?php

namespace Modules\UserFund\Repositories;

use App\Validators\GlobalValidator;
use Modules\UserFund\Models\Account;
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
        return GlobalValidator::class;
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

    public function updateAccount(Account $account, $attributes)
    {
        return $account->update($attributes);
    }

    public function deleteAccount(Account $account)
    {
        return $account->deleteAccount();
    }
}
