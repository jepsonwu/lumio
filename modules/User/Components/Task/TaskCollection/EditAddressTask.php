<?php

namespace Modules\User\Components\Task\TaskCollection;

use Modules\User\Constants\UserDBConstant;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 下午1:11
 */
class EditAddressTask extends EditAvatarTask
{
    public function getType()
    {
        return "task_edit_address";
    }

    public function getName()
    {
        return "编辑地区";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/4AE7CE6E-0FBB-F80A-9844-2ADEB1C83991-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/D5D30E03-AA22-D4EA-4115-D1A5974A3B4B-1MVZAnQ.jpg";
    }

    public function getCoinNumber()
    {
        return 2;
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_EDIT_ADDRESS;
    }
}
