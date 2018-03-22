<?php

namespace Modules\Admin\Constants;

use Modules\Admin\Models\User;

class UserConstant
{
    public static $data = [
        1 => [
            "id" => 1,
            "userName" => "admin",
            "password" => "admin@!@#",//md5(jepson+几号),
            "role" => User::ROLE_ADMIN,
        ],
        2 => [
            "id" => 2,
            "userName" => "jepson",
            "password" => "jepson@!@#",//md5(jepson+几号),
            "role" => User::ROLE_ADMIN,
        ]
    ];

    public static $userNameMap = [
        "admin" => 1,
        "jepson" => 2,
    ];
}