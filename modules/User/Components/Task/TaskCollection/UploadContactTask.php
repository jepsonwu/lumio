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
class UploadContactTask extends AbstractTask
{
    public function getType()
    {
        return "task_contact";
    }

    public function getName()
    {
        return "上传通讯录";
    }

    public function getIcon()
    {
        return "";
    }

    public function getCoinNumber()
    {
        return 10;
    }

    public function getUserGoldOperation()
    {
        return UserDBConstant::USER_GOLD_OPERATION_UPLOAD_CONTACT;
    }

    public function getProtocol()
    {
        return $this->buildProtocol(GlobalProtocolConstant::IN_CENTER_UPLOAD_CONTRACT);
    }

    public function isValid()
    {
        return $this->isGrey() ? false : true;
    }
}
