<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/24
 * Time: 17:34
 */

namespace Modules\User\Contracts;


use Modules\User\Components\Extensions\AppExtension;
use Modules\User\Components\Extensions\LoginExtension;

interface UserExtensionsInternalServiceContract
{
    /**
     * @param $userId
     * @return AppExtension
     */
    public function getApp($userId);

    /**
     * @param $userId
     * @return LoginExtension
     */
    public function getLogin($userId);
}