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

class BindQqTask extends BindWeiboTask
{
    public function getType()
    {
        return "task_bind_qq";
    }

    public function getName()
    {
        return "绑定QQ";
    }

    public function getIcon()
    {
        return "";
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_BIND_QQ;
    }

    public function getProtocol()
    {
        return $this->buildProtocol(GlobalProtocolConstant::USER_CENTER_ACCOUNT_SECURE_BIND_QQ);
    }

    public function isValid()
    {
        return $this->isGrey() ? false : true;
    }
}
