<?php

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/6
 * Time: 上午10:50
 */

namespace Modules\User\Services;

use Modules\User\Components\Extensions\ExtensionFactory;

class UserExtensionService
{
    /**
     * @param $userId
     * @return \Modules\User\Components\Extensions\AppExtension
     */
    public function getApp($userId)
    {
        return ExtensionFactory::App($userId);
    }

    /**
     * @param $userId
     * @return \Modules\User\Components\Extensions\LoginExtension
     */
    public function getLogin($userId)
    {
        return ExtensionFactory::Login($userId);
    }
}