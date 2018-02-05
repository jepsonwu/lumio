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
class FirstWatchTask extends AbstractTask
{
    public function getType()
    {
        return "task_first_watch";
    }

    public function getName()
    {
        return "首次关注";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/71CE76A0-19C0-A36A-9CB0-1150BF305D82-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/A41109CF-E7AA-A1D2-93B0-315DE952F688-1MVZAnQ.jpg";
    }

    public function getCoinNumber()
    {
        return 2;
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_FIRST_WATCH;
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
