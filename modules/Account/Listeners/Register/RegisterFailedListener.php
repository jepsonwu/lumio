<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/21
 * Time: 15:25
 */

namespace Modules\Account\Listeners\Register;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;
use Jiuyan\Common\Component\InFramework\Listeners\BaseListener;
use Modules\Account\Components\AccountQueueComponent;
use Modules\Account\Constants\AccountBusinessConstant;

class RegisterFailedListener extends BaseListener
{
    public function handle(BaseEvent $event)
    {
        $generalParams = $event->getRequestGeneralParams();
        AccountQueueComponent::smsSendStatus([
            'type' => AccountBusinessConstant::COMMON_SYS_QUEUE_MSG_TYPE_SMS_LOG,
            'stat_type' => AccountBusinessConstant::COMMON_REGISTER_SMS_SEND_STAT_REGISTER_ERROR,
            'mobile' => $generalParams['mobile'],
        ]);
    }
}