<?php

namespace Modules\Admin\Services;


use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Admin\Constants\AccountBanyanDBConstant;
use Modules\Admin\Constants\UserConstant;
use Modules\Admin\Models\User;


class UserService extends BaseService
{
    const TOKEN_EXPIRES = 600;

    /**
     * @param $username
     * @return User|null
     */
    public function getByName($username)
    {
        $user = array_get(UserConstant::$data, array_get(UserConstant::$userNameMap, $username, 0), []);
        return $user ? new User($user) : null;
    }

    public function getById($id)
    {
        $user = array_get(UserConstant::$data, $id, []);
        return $user ? new User($user) : null;
    }

    public function getIdByInviteCode($inviteCode)
    {
        $userId = (int)AccountBanyanDBConstant::commonAccountUserInviteCodeMap()->get($inviteCode);
        return $this->getById($userId);
    }


    /**
     * @param $token
     * @return User|null
     */
    public function getByToken($token)
    {
        $data = AccountBanyanDBConstant::commonAccountUserLoginToken()->get($token);
        $data = json_decode($data, true);
        $data || $data = [];

        $valid = array_get($data, "valid", 0);
        $userId = $valid > time() ? array_get($data, "id", 0) : 0;

        $user = $this->getById($userId);
        $user && $user->setToken($token);
        return $user;
    }

    public function generateToken(User $user)
    {
        $token = md5(serialize($user) . time() . rand(1, 99999));
        $data = [
            "id" => $user->id,
            "valid" => time() + self::TOKEN_EXPIRES
        ];
        AccountBanyanDBConstant::commonAccountUserLoginToken()->set($token, json_encode($data));
        $user->setToken($token);
    }
}