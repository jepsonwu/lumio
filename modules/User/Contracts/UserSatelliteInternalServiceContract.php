<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/12
 * Time: 14:37
 */

namespace Modules\User\Contracts;

interface UserSatelliteInternalServiceContract
{
    public function updateUserPasterLog($userId);
}