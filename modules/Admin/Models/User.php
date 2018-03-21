<?php

namespace Modules\Admin\Models;

class User
{
    const ROLE_ADMIN = 1;

    public $id;

    public $userName;

    public $password;

    public $token;

    public $role;

    public function __construct($user)
    {
        foreach ($user as $key => $value) {
            $this->$key = $value;
        }
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

