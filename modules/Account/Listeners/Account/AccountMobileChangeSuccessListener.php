<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/6
 * Time: 19:49
 */

namespace Modules\Account\Listeners\Account;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;
use Jiuyan\Common\Component\InFramework\Listeners\BaseListener;
use Modules\Account\Components\AccountQueueComponent;
use Modules\Account\Constants\AccountBusinessConstant;
use Modules\Account\Services\UserService;
use Modules\User\Contracts\UserTaskInternalServiceContract;

class AccountMobileChangeSuccessListener extends BaseListener
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
        $currentUser = $generalParams['currentUser'];
        $existsUser = $generalParams['existsUser'];
        $forceOpe = $generalParams['forceOpe'];
        $this->userTaskService->finishAuth($currentUser->id);
        AccountQueueComponent::userAuthFinishNotice([
            'auth_user_id' => $currentUser->id,
            'auth_mobile' => $generalParams['mobile'],
            'send_notice' => false,
            'source_id' => '',
            'relative_user_id' => $existsUser->id ?? 0,
            'relative_user_source_id' => $existsUser->source_id ?? '',
        ]);

        /**
         * 只有强制进行绑定的时候，才需要更新用户搜索池
         */
        if ($forceOpe) {
            $this->userService->updateUserSearchPool([
                'id' => $existsUser->id,
                'name' => ''
            ]);
        }
        AccountQueueComponent::userMobielChangeNotice([
            'auth_user_id' => $currentUser->id,
            'auth_mobile' => $generalParams['mobile'],
            'send_notice' => false
        ]);
    }
}