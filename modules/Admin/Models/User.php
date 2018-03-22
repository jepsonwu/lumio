<?php

namespace Modules\Admin\Models;

use Jiuyan\Tools\Business\EncryptTool;
use Modules\Admin\Constants\AccountBanyanDBConstant;

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
        $this->initInviteCode();
    }

    protected function initInviteCode()
    {
        $banyan = AccountBanyanDBConstant::commonAccountUserInviteCode();
        $inviteCode = $banyan->get($this->id);
        if (!$inviteCode) {
            $inviteCode = strtoupper(EncryptTool::encryptId(time() . rand(10, 99)));
            $banyan->set($this->id, $inviteCode);
            AccountBanyanDBConstant::commonAccountUserInviteCodeMap()->set($inviteCode, $this->id);
        }

        $this->inviteCode = $inviteCode;
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

