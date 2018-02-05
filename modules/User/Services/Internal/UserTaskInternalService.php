<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/24
 * Time: 17:36
 */

namespace Modules\User\Services\Internal;

use Modules\User\Contracts\UserTaskInternalServiceContract;
use Modules\User\Services\UserTaskService;

class UserTaskInternalService implements UserTaskInternalServiceContract
{
    /**
     * @var UserTaskService
     */
    public $userTaskService;

    protected $_inUserTaskService;

    public function __construct(UserTaskService $userTaskService)
    {
        $this->userTaskService = $userTaskService;
        $this->_inUserTaskService = app('InUserTaskService');
    }

    public function finishBindWeixin($userId)
    {
        //$this->userTaskService->finishBindWeixinTask($userId);
        $ret = $this->_inUserTaskService->finishBindWeixin($userId);
        return $ret;
    }

    public function finishBindWeibo($userId)
    {
        //$this->userTaskService->finishBindWeibo($userId);
        $this->_inUserTaskService->finishBindWeibo($userId);
    }

    public function finishBindQq($userId)
    {
        //$this->userTaskService->finishBindQq($userId);
        $this->_inUserTaskService->finishBindQq($userId);
    }

    public function finishNewUserGuide($userId)
    {
        $this->userTaskService->finishNewUserGuide($userId);
    }

    public function finishAuth($userId)
    {
        //$this->userTaskService->finishAuth($userId);
        return $this->_inUserTaskService->finishAuth($userId);
    }
}