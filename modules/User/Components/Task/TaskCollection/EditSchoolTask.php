<?php

namespace Modules\User\Components\Task\TaskCollection;

use Modules\User\Constants\UserDBConstant;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 下午1:11
 */
class EditSchoolTask extends EditBirthdayTask
{
    public function getType()
    {
        return "task_edit_school";
    }

    public function getName()
    {
        return "编辑学校";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/BD8535A2-A234-8F82-0FA4-EF5EE6E86AB9-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/C524B007-044A-95E1-82CA-0E67D7E733CF-1MVZAnQ.jpg";
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_EDIT_SCHOOL;
    }
}
