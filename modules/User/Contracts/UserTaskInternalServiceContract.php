<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/24
 * Time: 17:34
 */

namespace Modules\User\Contracts;


interface UserTaskInternalServiceContract
{
    public function finishBindWeibo($userId);
    public function finishBindQq($userId);
    public function finishBindWeixin($userId);

    public function finishNewUserGuide($userId);

    public function finishAuth($userId);
}