<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/12
 * Time: 13:57
 */

namespace Modules\User\Services;

use Jiuyan\Common\Component\InFramework\Services\BaseService;

class UserPasterService extends BaseService
{
    protected $_userPasterService;

    public function __construct()
    {
        $this->_userPasterService = app('InUserPasterService');
    }

    public function updatePasterLog($userId)
    {
        return $this->_userPasterService->updateUserPasterLog($userId);
    }
}