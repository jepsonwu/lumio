<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/6
 * Time: 14:57
 */

namespace Modules\Account\Listeners\Account;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;
use Jiuyan\Common\Component\InFramework\Listeners\BaseListener;
use Modules\Account\Components\AccountQueueComponent;
use Modules\Account\Constants\AccountBusinessConstant;

class AccountCaptchaCheckFailedListener extends BaseListener
{
    public function handle(BaseEvent $event)
    {
        $generalParams = $event->getRequestGeneralParams();
        return AccountQueueComponent::smsSendStatus([
            'type' => AccountBusinessConstant::COMMON_SYS_QUEUE_MSG_TYPE_SMS_LOG,
            'stat_type' => AccountBusinessConstant::COMMON_REGISTER_SMS_SEND_STAT_CHECK_FAILED,
            'mobile' => $generalParams['mobile'],
        ]);
    }
}