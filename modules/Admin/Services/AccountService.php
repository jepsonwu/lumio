<?php

namespace Modules\Admin\Services;


use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Admin\Constants\AccountErrorConstant;
use Modules\Admin\Models\User;


class AccountService extends BaseService
{
    protected $_userService;

    public function __construct(UserService $userService)
    {
        $this->_userService = $userService;
    }

    /**
     * @param $userName
     * @param $password
     * @return User|null
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function login($userName, $password)
    {
        $user = $this->_userService->getByName($userName);
        (!$user || !$this->checkPassword($user, $password))
        && ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_USER_ACCOUNT_PASSWORD_WRONG);

        $this->_userService->generateToken($user);

        return $user;
    }

    protected function checkPassword(User $user, $password)
    {
        $day = date("d", time());
        return md5($user->password . $day) == md5($password);
    }
}
