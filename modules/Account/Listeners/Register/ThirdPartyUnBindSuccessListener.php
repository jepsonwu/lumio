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
use Modules\User\Contracts\UserTaskInternalServiceContract;

class ThirdPartyUnBindSuccessListener extends BaseListener
{
    public $userTaskService;

    public function __construct(UserTaskInternalServiceContract $userTaskService)
    {
        $this->userTaskService = $userTaskService;
    }

    public function handle(BaseEvent $event)
    {
        $generalParams = $event->getRequestGeneralParams();
        $currentUserSourceId = $generalParams['thirdPartyUserId'];
        $currentUser = $generalParams['currentUser'];
        $thirdPartyFlag = $generalParams['thirdPartyFlag'];
        if ($thirdPartyFlag == AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_WEIBO) {
            AccountQueueComponent::userActionForThirdPartyBind([
                'bind_id' => 'w' . $currentUserSourceId,
                'user_id' => $currentUser->id,
                'action' => 'unbound',
                'type' => 'w',
            ]);
        } elseif ($thirdPartyFlag == AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_WEIXIN) {
            AccountQueueComponent::userActionForThirdPartyBind([
                'bind_id' => $currentUserSourceId,
                'user_id' => $currentUser->id,
                'action' => 'unbound',
                'type' => 'x',
            ]);
        }
    }
}