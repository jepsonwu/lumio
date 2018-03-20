<?php

namespace Modules\Admin\Services;


use Jiuyan\Common\Component\InFramework\Services\BaseService;


class AccountService extends BaseService
{
    protected $_userService;

    public function __construct(UserService $userService)
    {
        $this->_userService = $userService;
    }

    public function login($userName, $password)
    {
        //todo 用户信息
        return [
            "id" => 1,
            "user_name" => "jepson",
            "role" => 1,
            "token" => md5('jepson')
        ];
    }
}
