<?php

namespace Modules\Admin\Models;

use Jiuyan\Tools\Business\EncryptTool;

class User
{
    const ROLE_ADMIN = 1;

    public $id;

    public $userName;

    public $password;

    public $token;

    public $role;

    public $inviteCode;

    public function __construct($user)
    {
        foreach ($user as $key => $value) {
            $this->$key = $value;
        }
        $this->inviteCode = strtoupper(EncryptTool::encryptId(time() . rand(10, 99)));//todo 固定邀请码
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function isAdmin()
    {
        return $this->role == self::ROLE_ADMIN;
    }
}

