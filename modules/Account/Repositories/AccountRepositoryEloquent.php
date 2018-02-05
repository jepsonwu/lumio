<?php

namespace Modules\Account\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Modules\Account\Repositories\AccountRepository;
use Modules\Account\Models\Account;
use Modules\Account\Validators\AccountValidator;

/**
 * Class AccountRepositoryEloquent
 * @package namespace Modules\Account\Repositories;
 */
class AccountRepositoryEloquent extends BaseRepository implements AccountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Account::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return AccountValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        /*
         * 禁用RequestCriteria, 保证http和artisan两种模式下缓存一致
         */
        //$this->pushCriteria(app(RequestCriteria::class));
    }
}
