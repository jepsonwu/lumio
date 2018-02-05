<?php

namespace Modules\User\Repositories;

/**
 * Interface UserTaskRepository
 * @package namespace Modules\User\Repositories;
 */
interface UserTaskRepository
{
    public function finishTask($taskFlag, $userId);
}
