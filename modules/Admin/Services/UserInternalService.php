<?php

namespace Modules\Admin\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Jiuyan\LumioSSO\Contracts\AuthenticateAdminContract;
use Modules\Admin\Constants\AccountBusinessConstant;

class UserInternalService extends BaseService implements AuthenticateAdminContract
{
    /**
     * @var UserService
     */
    public $userService;

    private $mock = false;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getLoginUrl($uri)
    {
        //html地址
        return env("APP_DOMAIN") . '/admin/auth/login?callback=' . urlencode($uri);
    }

    public function getLoginUser()
    {
        if (!$this->mock) {
            if (!($userToken = app('request')->input('_token')) &&
                !($userToken = app('request')->cookie(AccountBusinessConstant::ACCOUNT_AUTHORIZED_COOKIE_TOKEN))
            ) {
                return [];
            }

            return $this->userService->getByToken($userToken);
        }
        return config('admin_auth.mock_user');
    }

    public function setMock($mock)
    {
        $this->mock = $mock;
    }

    /**
     * @param $userId
     * @return \Modules\Admin\Models\User|null
     */
    public function isValidUser($userId)
    {
        return $this->userService->getById($userId);
    }
}


