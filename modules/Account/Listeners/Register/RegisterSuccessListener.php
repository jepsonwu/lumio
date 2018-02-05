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

class RegisterSuccessListener extends BaseListener
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
        $forceRegisterFlag = $generalParams['force'] ?? false;
        $existsUser = $generalParams['existsUser'] ?? [];
        $registerUser = $generalParams['currentUser'];
        AccountQueueComponent::smsSendStatus([
            'type' => AccountBusinessConstant::COMMON_SYS_QUEUE_MSG_TYPE_SMS_LOG,
            'stat_type' => AccountBusinessConstant::COMMON_REGISTER_SMS_SEND_STAT_REGISTER_NEW,
            'mobile' => $generalParams['mobile'],
        ]);
        AccountQueueComponent::userActionForLogin([
            'user_id' => $registerUser->id,
            'source' => 0,
            'source_id' => '',
            'first' => true,
            'version' => $commonParams['version'] ?? '',
            'idfa' => $commonParams['idfa'] ?? '',
            'tdid' => $commonParams['tdId'] ?? '',
            'imei' => $commonParams['imei'] ?? '',
        ]);
        AccountQueueComponent::userAuthFinishNotice([
            'auth_user_id' => $registerUser->id,
            'auth_mobile' => $generalParams['mobile'],
            'send_notice' => false,
            'source_id' => '',
            'relative_user_id' => $existsUser->id ?? 0,
            'relative_user_source_id' => $existsUser->source_id ?? '',
        ]);
        $this->userTaskService->finishAuth($registerUser->id);
        if ($forceRegisterFlag) {
            //TODO 更新solr中的用户搜索数据
            $this->userService->updateUserSearchPool([
                'id'
            ]);
        }
    }
}