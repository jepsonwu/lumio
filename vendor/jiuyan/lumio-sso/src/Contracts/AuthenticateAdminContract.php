<?php
namespace Jiuyan\LumioSSO\Contracts;

interface AuthenticateAdminContract extends AuthenticateContract
{
    public function Logout();
}