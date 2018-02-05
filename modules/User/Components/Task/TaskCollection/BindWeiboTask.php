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
class BindWeiboTask extends AbstractTask
{
    public function getType()
    {
        return "task_bind_weibo";
    }

    public function getName()
    {
        return "绑定微博";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/92DE989B-AE1F-5291-A381-FD070C0AFF7A-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/EAF20EC8-4D86-D54D-D25F-0DF8B21711D3-1MVZAnQ.jpg";
    }

    public function getCoinNumber()
    {
        return 5;
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_BIND_WEIBO;
    }

    public function getProtocol()
    {
        return $this->buildProtocol(GlobalProtocolConstant::USER_CENTER_ACCOUNT_SECURE_BIND_WEIBO);
    }
}
