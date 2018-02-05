<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 上午11:37
 */

namespace Modules\User\Components\Task\TaskCollection;

use App\Constants\GlobalProtocolConstant;
use Modules\User\Constants\UserDBConstant;

class AuthTask extends AbstractTask
{
    public function getType()
    {
        return "task_auth";
    }

    public function getName()
    {
        return "绑定手机";
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
        return UserDBConstant::USER_GOLD_OPERATION_AUTH;
    }

    public function getProtocol()
    {
        return $this->buildProtocol(GlobalProtocolConstant::IN_CENTER_AUTH);
    }

    public function isValid()
    {
        return $this->isGrey() ? false : true;
    }
}
