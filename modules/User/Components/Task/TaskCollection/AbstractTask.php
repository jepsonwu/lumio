<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 上午11:07
 */

namespace Modules\User\Components\Task\TaskCollection;

use Jiuyan\Tools\Business\ProtocolTool;
use Modules\User\Constants\UserBanyanDBConstant;
use Modules\User\Services\UserExtensionService;
use Modules\User\Services\UserGoldService;

abstract class AbstractTask implements Task
{
    private $userId;

    public function __construct($userId)
    {
        $this->setUserId($userId);
    }

    public function getDesc()
    {
        return "";
    }

    public function getInfo()
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'icon' => $this->getIcon(),
            'coin' => $this->getCoinNumber(),
            'action' => $this->getProtocol(),
            'is_finished' => $this->isFinished(),
            //'desc' => $this->getDesc(),
        ];
    }

    public function isValid()
    {
        return true;
    }

    public function finish()
    {
        if ($this->isFinished() || !$this->isValidFinish()) {
            return true;
        }

        //double coin
        $coinNumber = $this->getCoinNumber();

        $registerDay = $this->userExtensionService()->getLogin($this->getUserId())->getRegisterDays();
        $this->isGrey() && $registerDay <= 3 && $registerDay > 0 && $coinNumber = $coinNumber * 2;

        $result = $this->userGoldService()->earn(
            $this->getUserId(),
            $coinNumber,
            $this->getUserGoldOperation(),
            $this->getName()
        );

        if ($result) {
//            $template = $isGrey
//                ? NNoticeTypeTplConstant::SYS_MSG_USER_TASK_FINISHED_NEW
//                : NNoticeTypeTplConstant::SYS_MSG_USER_TASK_FINISHED;
//
//            if (!SystemHelper::isDevelopment()) {
//                NServiceFactory::Notice()->sendSystem(
//                    $userId,
//                    [
//                        'name' => $this->getName(),
//                        'number' => $this->getCoinNumber(),
//                    ],
//                    0,
//                    $template
//                );
//            }


            $this->getStatusStorage()->set($this->getType(), 1);

            //NServiceFactory::User()->checkUpgrade($userId, '', true);
        }

        return true;
    }

    protected function isGrey()
    {
        $version = $this->userExtensionService()->getApp($this->getUserId())->version;
        return in_array($this->getUserId(), [7, 8]) && version_compare($version, "3.2.40", ">=");
    }

    /**
     * @return UserExtensionService
     */
    protected function userExtensionService()
    {
        return app(UserExtensionService::class);
    }

    /**
     * @return UserGoldService
     */
    protected function userGoldService()
    {
        return app(UserGoldService::class);
    }

    protected function setUserId($userId)
    {
        $this->userId = $userId;
    }

    protected function getUserId()
    {
        return $this->userId;
    }

    protected function isValidFinish()
    {
        return true;
    }

    public function isFinished()
    {
        return (bool)$this->getStatusStorage()->get($this->getType());
    }

    protected function buildProtocol($protocol, array $params = [])
    {
        return ProtocolTool::h5Compatibility($protocol, $params);
    }

    public function getStatusStorage()
    {
        return UserBanyanDBConstant::userTaskStatus($this->getUserId());
    }
}
