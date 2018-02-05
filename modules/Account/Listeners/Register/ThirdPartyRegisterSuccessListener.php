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
use Modules\Account\Services\UserService;
use Modules\User\Contracts\UserTaskInternalServiceContract;

class ThirdPartyRegisterSuccessListener extends BaseListener
{
    public $userTaskService;
    public $userService;

    public function __construct(UserTaskInternalServiceContract $userTaskService, UserService $userService)
    {
        $this->userTaskService = $userTaskService;
        $this->userService = $userService;
    }

    public function handle(BaseEvent $event)
    {
        $generalParams = $event->getRequestGeneralParams();
        $commonParams = $event->getRequestCommonParasm();
        $authUser = $generalParams['authUser'];
        AccountQueueComponent::userActionForLogin([
            'user_id' => $authUser->id,
            'source' => 0,
            'source_id' => '',
            'first' => true,
            'version' => $commonParams['version'] ?? '',
            'idfa' => $commonParams['idfa'] ?? '',
            'tdid' => $commonParams['tdId'] ?? '',
            'imei' => $commonParams['imei'] ?? '',
        ]);
        $this->userTaskService->finishNewUserGuide($authUser->id);
        $this->userService->updateUserSearchPool([
            'id' => $authUser->id,
            'created_at' => $authUser->created_at
        ]);
    }
}