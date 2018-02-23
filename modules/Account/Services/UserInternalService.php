<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/22
 * Time: 16:07
 */

namespace Modules\Account\Services;

use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Jiuyan\LumioSSO\Contracts\AuthenticateContract;
use Modules\Account\Constants\AccountBusinessConstant;
use Modules\Account\Constants\AccountErrorConstant;
use Modules\Account\Models\User;

class UserInternalService extends BaseService implements AuthenticateContract
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

    public function getUserById($userId)
    {
        return $this->userService->getById($userId);
    }

    public function getUserByToken($token)
    {
        return $this->userService->getUserByToken($token);
    }

    public function getLoginUser()
    {
        if (!$this->mock) {
            if (!($userToken = app('request')->input('_token')) &&
                !($userToken = app('request')->cookie(AccountBusinessConstant::ACCOUNT_AUTHORIZED_COOKIE_COMMON_KEY)) &&
                !($userToken = app('request')->cookie(AccountBusinessConstant::ACCOUNT_AUTHORIZED_COOKIE_SPE_KEY))) {
                ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_AUTHORIZED_FAILED);
            }
            return $this->getUserByToken($userToken);
        }
        return new User(config('api_auth.mock_user'));
    }

    public function setMock($mock)
    {
        $this->mock = $mock;
    }

    public function isSeller($userId)
    {
        $user = $this->userService->isValidById($userId);

        return $this->userService->isSeller($user);
    }

    public function isBuyer($userId)
    {
        $user = $this->userService->isValidById($userId);
        return $this->userService->isBuyer($user);
    }

    public function isDeployTaobaoAccount($userId)
    {
        $user = $this->userService->isValidById($userId);
        return $this->userService->isDeployTaobaoAccount($user);
    }

    public function isDeployJdAccount($userId)
    {
        $user = $this->userService->isValidById($userId);
        return $this->userService->isDeployJdAccount($user);
    }
}


