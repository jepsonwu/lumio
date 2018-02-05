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

class ThirdPartyLoginSuccessListener extends BaseListener
{
    public $userTaskService;

    public function __construct(UserTaskInternalServiceContract $userTaskService)
    {
        $this->userTaskService = $userTaskService;
    }

    public function handle(BaseEvent $event)
    {
        $generalParams = $event->getRequestGeneralParams();
        $commonParams = $event->getRequestCommonParasm();
        AccountQueueComponent::userActionForLogin([
            'user_id' => $generalParams['authUser']->id,
            'source' => 0,
            'source_id' => '',
            'first' => false,
            'version' => $commonParams['version'] ?? '',
            'idfa' => $commonParams['idfa'] ?? '',
            'tdid' => $commonParams['tdId'] ?? '',
            'imei' => $commonParams['imei'] ?? '',
        ]);
    }
}