<?php

namespace Modules\User\Components\Task\TaskCollection;

use Modules\User\Constants\UserDBConstant;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 上午11:37
 */
class EditPersonalTagTask extends EditAvatarTask
{
    public function getType()
    {
        return "task_personal_tag";
    }

    public function getName()
    {
        return "编辑个性标签";
    }

    public function getIcon()
    {
        return "";
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_EDIT_PERSONAL_TAG;
    }

    public function isValid()
    {
        return $this->isGrey() ? false : true;
    }
}
