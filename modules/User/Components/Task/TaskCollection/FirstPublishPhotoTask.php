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
class FirstPublishPhotoTask extends AbstractTask
{
    public function getType()
    {
        return "task_first_send_photo";
    }

    public function getName()
    {
        return "首次发图";
    }

    public function getIcon()
    {
        return $this->isFinished()
            ? "http://bchat.jiuyan.info//bc/2017/10/23/5B6FB48D-5BE3-5742-7592-281555A67875-1MVZAnQ.jpg"
            : "http://bchat.jiuyan.info//bc/2017/10/23/9B942F40-D988-D59B-207C-D0A6097ADA61-1MVZAnQ.jpg";
    }

    public function getCoinNumber()
    {
        return 5;
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_FIRST_PUBLISH;
    }

    public function getProtocol()
    {
        return $this->buildProtocol(GlobalProtocolConstant::CAMERA_ALBUM);
    }
}
