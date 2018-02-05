<?php

namespace Modules\User\Components\Task\TaskCollection;

use App\Constants\GlobalProtocolConstant;
use Modules\User\Constants\UserDBConstant;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 上午11:37
 */
class EditAvatarTask extends AbstractTask
{
    public function getType()
    {
        return "task_edit_avatar";
    }

    public function getName()
    {
        return "编辑头像";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/366E5406-38D6-DB88-B493-BEA3CA20AD16-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/58B38EB7-D679-935A-15E0-7A137ED2F8BA-1MVZAnQ.jpg";
    }

    public function getCoinNumber()
    {
        return 3;
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_EDIT_AVATAR;
    }

    public function getProtocol()
    {
        return $this->buildProtocol(GlobalProtocolConstant::USER_CENTER_EDIT_PROFILE, [
            "refresh" => 1
        ]);
    }

    public function isValid()
    {
        return $this->isGrey() ? true : false;
    }
}
