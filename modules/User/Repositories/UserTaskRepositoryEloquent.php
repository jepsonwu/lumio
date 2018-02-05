<?php

namespace Modules\User\Repositories;

use Jiuyan\Common\Component\InFramework\Components\InThriftRepositoryBaseComponent;
use Modules\User\Models\UserTask;

/**
 * Class UserTaskRepositoryEloquent
 * @package namespace Modules\User\Repositories;
 */
class UserTaskRepositoryEloquent extends InThriftRepositoryBaseComponent implements UserTaskRepository
{
    public function __construct()
    {
        $this->modelHandle = app('InUserTaskService');
    }

    protected function _convertCollection($items = [])
    {
        return new UserTask($items);
    }

    public function finishTask($taskFlag, $userId)
    {
        $inServiceFuncName = 'finish' . ucfirst($taskFlag);
        $ret = $this->modelHandle->{$inServiceFuncName}($userId);
        return $this->_convertCollection($this->modelHandle->{$inServiceFuncName}($userId));
    }
}
