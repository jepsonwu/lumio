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
class FirstZanTask extends AbstractTask
{
    public function getType()
    {
        return "task_first_zan";
    }

    public function getName()
    {
        return "首次点赞";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/B01028DC-48DD-4ECD-3957-6B0EA170B15E-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/49E3DDAB-9F67-7740-7BBC-6547210551E8-1MVZAnQ.jpg";
    }

    public function getCoinNumber()
    {
        return 2;
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_FIRST_ZAN;
    }

    public function getProtocol()
    {
        return $this->buildProtocol(GlobalProtocolConstant::MAIN_DISCOVER_WORLD);
    }

    public function isValid()
    {
        return $this->isGrey() ? true : false;
    }
}
