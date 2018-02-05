<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/21
 * Time: 19:01
 */

namespace Modules\Account\Components;

use Jiuyan\Common\Component\InFramework\Components\CommonQueueDispatchComponent;
use Modules\Account\Jobs\AccountSmsSendStatusJob;
use Modules\Account\Jobs\AccountUserActionForFirstLoginJob;
use Modules\Account\Jobs\AccountUserActionForThirdPartyBindJob;
use Modules\Account\Jobs\AccountUserAuthFinishedNoticeJob;
use Modules\Account\Jobs\UserMobileChangeNoticeJob;
use Modules\Account\Jobs\UserSearchPoolJob;

class AccountQueueComponent extends CommonQueueDispatchComponent
{
    public static function smsSendStatus($statusInfo)
    {
        return self::getInstance(AccountSmsSendStatusJob::class)->pushMsg($statusInfo);
    }

    public static function userActionForLogin($actionInfo)
    {
        return self::getInstance(AccountUserActionForFirstLoginJob::class)->pushMsg($actionInfo);
    }

    public static function userAuthFinishNotice($noticeInfo)
    {
        return self::getInstance(AccountUserAuthFinishedNoticeJob::class)->pushMsg($noticeInfo);
    }

    public static function userActionForThirdPartyBind($actionInfo)
    {
        return self::getInstance(AccountUserActionForThirdPartyBindJob::class)->pushMsg($actionInfo);
    }

    public static function userSearchPool($actionInfo)
    {
        return self::getInstance(UserSearchPoolJob::class)->pushMsg($actionInfo);
    }

    public static function userMobielChangeNotice($noticeInfo)
    {
        return self::getInstance(UserMobileChangeNoticeJob::class)->pushMsg($noticeInfo);
    }
}