<?php

namespace Modules\Account\Repositories;

use Modules\Account\Models\User;
use Modules\Account\Validators\UserValidator;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class UserRepositoryEloquent
 * @package namespace Modules\Account\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository
{
    /**
     * @return User
     */
    public function model()
    {
        return User::class;
    }

    public function validator()
    {
        return UserValidator::class;
    }

    public function createUser()
    {
        $this->resetModel();
    }

    public function getByMobile($mobile)
    {
        return $this->model()::whereMobile($this->model->newQuery(), $mobile)->first();
    }
}
