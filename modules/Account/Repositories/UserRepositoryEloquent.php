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

    /**
     * @param $token
     * @return \Illuminate\Database\Eloquent\Model|null|static|User
     */
    public function getByToken($token)
    {
        return $this->model()::whereToken($this->model->newQuery(), $token)->first();
    }

    public function changePassword(User $user, $password)
    {
        return $user->changePassword($password);
    }

    public function isBuyer(User $user)
    {
        return $user->isBuyer();
    }

    public function isSeller(User $user)
    {
        return $user->isSeller();
    }
}
