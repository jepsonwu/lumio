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
class BindWeixinTask extends BindWeiboTask
{
    public function getType()
    {
        return "task_bind_weixin";
    }

    public function getName()
    {
        return "绑定微信";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/64B79DDB-D925-6C7F-7B35-F3081320090B-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/E90F451C-FC09-8F28-57D0-F1E4F31ED57E-1MVZAnQ.jpg";
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_BIND_WEIXIN;
    }

    public function getProtocol()
    {
        return $this->buildProtocol(GlobalProtocolConstant::USER_CENTER_ACCOUNT_SECURE_BIND_WEIXIN);
    }
}
