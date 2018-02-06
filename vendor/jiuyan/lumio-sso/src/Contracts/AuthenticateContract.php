<?php
namespace Jiuyan\LumioSSO\Contracts;

interface AuthenticateContract
{
    public function getLoginUser();

    public function setMock($mock);
}