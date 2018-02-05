<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/7
 * Time: 16:25
 */

namespace Modules\Account\Listeners\Common;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;
use Jiuyan\Common\Component\InFramework\Listeners\BaseListener;
use Modules\Account\Constants\AccountBusinessConstant;
use Modules\User\Contracts\UserTaskInternalServiceContract;

class ThirdPartyBindTaskFinishListener extends BaseListener
{
    public $userTaskService;

    public function __construct(UserTaskInternalServiceContract $userTaskService)
    {
        $this->userTaskService = $userTaskService;
    }

    public function handle(BaseEvent $event)
    {
        $generalParams = $event->getRequestGeneralParams();
        $thirdPartyFlag = $generalParams['thirdPartyFlag'];
        $authUser = $generalParams['authUser'];
        switch ($thirdPartyFlag) {
            case AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_WEIBO:
                $this->userTaskService->finishBindWeibo($authUser->id);
                break;
            case AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_QQ:
                /**
                 * 暂时取消qq绑定的任务
                 */
                //$this->userTaskService->finishBindQq($authUser->id);
                break;
            case AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_WEIXIN:
                $this->userTaskService->finishBindWeixin($authUser->id);
                break;
        }
    }
}