<?php

namespace Modules\User\Components\Task\TaskCollection;

use Modules\User\Constants\UserDBConstant;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 上午11:37
 */
class EditNumberTask extends EditAvatarTask
{
    public function getType()
    {
        return "task_inumber";
    }

    public function getName()
    {
        return "修改in号";
    }

    public function getIcon()
    {
        return "";
    }

    public function getCoinNumber()
    {
        return 5;
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_EDIT_NUMBER;
    }

    public function isValid()
    {
        return $this->isGrey() ? false : true;
    }
}
