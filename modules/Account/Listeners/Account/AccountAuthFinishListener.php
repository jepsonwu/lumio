<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/12
 * Time: 12:04
 */

namespace Modules\Account\Listeners\Account;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;
use Jiuyan\Common\Component\InFramework\Listeners\BaseListener;
use Modules\User\Contracts\UserSatelliteInternalServiceContract;
use Log;

class AccountAuthFinishListener extends BaseListener
{
    public $userSatelliteService;

    public function __construct(UserSatelliteInternalServiceContract $userSatelliteInternalService)
    {
        $this->userSatelliteService = $userSatelliteInternalService;
    }

    public function handle(BaseEvent $event)
    {
        $generalParams = $event->getRequestGeneralParams();
        if (!$currentUser = $generalParams['currentUser']) {
            Log::error('auth finish listener is error');
            return false;
        }
        /**
         * 更新用户的贴纸使用记录
         */
        $this->userSatelliteService->updateUserPasterLog($currentUser->id);
        return true;
    }
}