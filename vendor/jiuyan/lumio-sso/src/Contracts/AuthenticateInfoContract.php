<?php

namespace Jiuyan\LumioSSO\Contracts;

interface AuthenticateInfoContract
{
    public function getUserInfoByToken($token);
    public function getUserInfoByThirdPartyId($sourceType, $openId);
}