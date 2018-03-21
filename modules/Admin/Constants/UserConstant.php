<?php

namespace Modules\Admin\Constants;

use Modules\Admin\Models\User;

class UserConstant
{
    public static $data = [
        1 => [
            "id" => 1,
            "userName" => "jepson",
            "password" => "jepson",//md5(jepson+几号),
            "role" => User::ROLE_ADMIN,
        ]
    ];

    public static $userNameMap = [
        "jepson" => 1
    ];
}