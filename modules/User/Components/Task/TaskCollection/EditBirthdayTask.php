<?php

namespace Modules\User\Components\Task\TaskCollection;

use App\Constants\GlobalProtocolConstant;
use Modules\User\Constants\UserDBConstant;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 下午1:11
 */
class EditBirthdayTask extends AbstractTask
{
    public function getType()
    {
        return "task_edit_birthday";
    }

    public function getName()
    {
        return "编辑生日";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/EF8C2458-23E0-FF58-3D5D-E37FB4A21EE7-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/C9AB9604-8302-4F04-2531-50F429D3164B-1MVZAnQ.jpg";
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_EDIT_BIRTHDAY;
    }

    public function getProtocol()
    {
        return $this->buildProtocol(GlobalProtocolConstant::USER_CENTER_EDIT_PROFILE, [
            "refresh" => 1
        ]);
    }

    public function getCoinNumber()
    {
        return 2;
    }
}
