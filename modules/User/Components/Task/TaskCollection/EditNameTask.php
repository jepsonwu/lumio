<?php

namespace Modules\User\Components\Task\TaskCollection;

use Modules\User\Constants\UserDBConstant;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 下午1:11
 */
class EditNameTask extends EditAvatarTask
{
    public function getType()
    {
        return "task_edit_name";
    }

    public function getName()
    {
        return "编辑昵称";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/EECFAA94-64A5-7C68-3B71-BBA469E55B11-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/780B07B7-C3A3-B7D6-2B26-23047788D821-1MVZAnQ.jpg";
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_EDIT_NAME;
    }
}
