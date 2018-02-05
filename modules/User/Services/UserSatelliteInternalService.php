<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/12
 * Time: 14:37
 */

namespace Modules\User\Services;

use Modules\User\Contracts\UserSatelliteInternalServiceContract;

class UserSatelliteInternalService implements UserSatelliteInternalServiceContract
{
    /**
     * @var UserPasterService
     */
    protected $_userPasterService;

    public function __construct(UserPasterService $userPasterService)
    {
        $this->_userPasterService = $userPasterService;
    }

    public function updateUserPasterLog($userId)
    {
        return $this->_userPasterService->updatePasterLog($userId);
    }
}