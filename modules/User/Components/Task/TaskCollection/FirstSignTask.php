<?php

namespace Modules\User\Components\Task\TaskCollection;

use Modules\User\Constants\UserDBConstant;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 上午11:37
 */
class FirstSignTask extends AbstractTask
{
    public function getType()
    {
        return "task_first_sign";
    }

    public function getName()
    {
        return "首次打卡";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/BDBC850A-96F2-CD2A-DA0F-6ABF17299464-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/8771BC1E-5823-5AE6-5417-2AFC13E117E9-1MVZAnQ.jpg";
    }

    public function getCoinNumber()
    {
        return 5;
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_FIRST_SIGN;
    }

    public function getProtocol()
    {
        return NProtocolConstant::buildH5PerfectProtocol(
            UrlCollect::getClockUrl(),
            "打卡"
        );
    }

    public function isValid()
    {
        return $this->isGrey() ? true : false;
    }

    public function isValidFinish()
    {
        if (!NServiceFactory::Clock()->isFirstClock($this->getUserId())) {
            return false;
        }

        return true;
    }
}
