<?php

namespace Modules\Account\Services;

use Jiuyan\Captcha\CaptchaComponent;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Account\Constants\AccountBanyanDBConstant;
use Modules\Account\Constants\AccountBusinessConstant;
use Modules\Account\Constants\AccountErrorConstant;
use Modules\Account\Repositories\AccountRepositoryEloquent;
use Log;

class AccountRequestService extends BaseService
{
    /**
     */
    protected $_repository;

    /**
     * @var UserService
     */
    protected $_userService;

    public function __construct(AccountRepositoryEloquent $repository, UserService $userService)
    {
        $this->_repository = $repository;
        $this->_userService = $userService;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function sendAccountCaptcha($mobile)
    {
        return true;
        //todo 提供新驱动
        return CaptchaComponent::getInstance()->sendCaptcha($mobile, '', 'sms');
        ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_SMS_CODE_SEND_FAILED);
    }

    public function register($requestParams)
    {
        $user = $this->_userService->getByMobile($requestParams['mobile']);
        return $user;

        $this->_checkPasswordAvailable($requestParams['password']);
        $this->_checkSmsCaptcha($requestParams['mobile'], $requestParams);

        //has user
        //todo register
        $user = $this->_repository->create([
            ""
        ]);

        ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_REGISTER_FAILED);
    }

    public function login($requestParams)
    {
        $user = [];
        return $user;
    }

    public function logout()
    {

    }

    public function changeAccountPassword($requestParams)
    {
        $currentUserId = $requestParams['currentUser']['id'];
        $password = $requestParams['password'];
        $newPassword = $requestParams['new_password'];
        return $this->_userService->changeAccountPassword($currentUserId, $password, $newPassword);

        ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_PASSWORD_SET_FAILED);
    }

    public function setAccountPassword($requestParams)
    {
        $currentUserId = $requestParams['currentUser']['id'];
        $password = $requestParams['password'];
        $this->_checkPasswordAvailable($password);
        return $this->_userService->setAccountPassword($currentUserId, $password);

        ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_PASSWORD_SET_FAILED);
    }

    public function resetAccountPassword($requestParams)
    {
        $currentMobile = $requestParams['mobile'];
        $newPassword = $requestParams['password'];
        //$this->_checkResetActionPermission($currentMobile);
        $this->_checkPasswordAvailable($newPassword);
        $this->_checkSmsCaptcha($currentMobile, $requestParams);

        //AccountErrorConstant::ERR_ACCOUNT_PASSWORD_RESET_FAILED
        if ($this->_userService->resetAccountPassword($currentMobile, $newPassword)) {
            return true;
        }
        return false;
    }


    private function _checkResetActionPermission($mobile)
    {
        $cacheHandle = AccountBanyanDBConstant::accountPasswordResetTimes();
        $resetTimes = $cacheHandle->get($mobile);
        if ($resetTimes) {
            if ($resetTimes >= AccountBusinessConstant::COMMON_ACCOUNT_PASSWORD_RESET_TIMES_LIMIT_PER_DAY) {
                Log::error('password-reset is too frequency mobile:' . $mobile . ' times:' . $resetTimes);
                ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_PASSWORD_RESET_FAILED);
            } else {
                $cacheHandle->inc($mobile, 1);
            }
        } else {
            $cacheHandle->set($mobile, 1, 86400);
        }
    }

    private function _checkSmsCaptcha($mobile, &$requestParams)
    {
        $captcha = $requestParams['code'] ?? '';
        if (!CaptchaComponent::getInstance()->verifyCaptcha($captcha, $mobile)) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_INVALID_SMS_CODE);
        }
        return true;
    }

    private function _checkPasswordAvailable($password)
    {
        if ($password &&
            (!preg_match(AccountBusinessConstant::COMMON_REGULAR_PASSWORD_FORMAT, $password, $pwdMatches) || !$pwdMatches)) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_VOICE_CODE_SEND_FAILED);
        }
    }
}
