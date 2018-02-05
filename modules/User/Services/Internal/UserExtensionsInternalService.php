<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/24
 * Time: 17:36
 */

namespace Modules\User\Services\Internal;

use Modules\User\Contracts\UserExtensionsInternalServiceContract;
use Modules\User\Services\UserExtensionService;

class UserExtensionsInternalService implements UserExtensionsInternalServiceContract
{
    protected $extensionService;

    public function __construct(UserExtensionService $extensionService)
    {
        $this->extensionService = $extensionService;
    }

    /**
     * @param $userId
     * @return \Modules\User\Components\Extensions\AppExtension
     */
    public function getApp($userId)
    {
        return $this->extensionService->getApp($userId);
    }

    /**
     * @param $userId
     * @return \Modules\User\Components\Extensions\LoginExtension
     */
    public function getLogin($userId)
    {
        return $this->extensionService->getLogin($userId);
    }
}