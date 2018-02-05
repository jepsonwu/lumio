<?php

namespace Modules\Account\Services;

use Jiuyan\Captcha\CaptchaComponent;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Modules\Account\Constants\AccountBanyanDBConstant;
use Modules\Account\Constants\AccountBusinessConstant;
use Modules\Account\Constants\AccountErrorConstant;
use Modules\Account\Models\User;
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
        $result = CaptchaComponent::getInstance()->sendCaptcha($mobile, '', 'sms');
        if (!$result) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_SMS_CODE_SEND_FAILED);
        }
    }

    public function register($requestParams)
    {
        $this->_checkPasswordAvailable($requestParams['password']);
        $this->_checkSmsCaptcha($requestParams['mobile'], $requestParams['code']);
        $this->_userService->getByMobile($requestParams['mobile'])
        && ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_USER_EXISTS);

        $user = $this->_userService->create($requestParams);
        $user || ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_REGISTER_FAILED);

        return $user;
    }

    public function login($mobile, $password)
    {
        $user = $this->_checkUserByMobile($mobile);
        $this->_checkPassword($user, $password);
        $user = $this->_userService->updateToken($user);
        $user === false && ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_LOGIN_FAILED);

        return $user;
    }

    public function logout()
    {

    }

    public function changePassword(User $user, $password, $newPassword)
    {
        $password == $newPassword && ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_PASSWORD_SAME_NEW_PASSWORD);
        $this->_checkPassword($user, $password);

        $result = $this->_userService->changePassword($user, $newPassword);
        $result || ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_PASSWORD_CHANGE_FAILED);
        return true;
    }

    public function resetPassword($mobile, $password, $captcha)
    {
        $user = $this->_checkUserByMobile($mobile);
        $this->_checkPasswordAvailable($password);
        $this->_checkSmsCaptcha($mobile, $captcha);

        $result = $this->_userService->changePassword($user, $password);
        $result || ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_PASSWORD_RESET_FAILED);
        return true;
    }

    private function _checkSmsCaptcha($mobile, $captcha)
    {
        return true;
        if (!CaptchaComponent::getInstance()->verifyCaptcha($captcha, $mobile)) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_INVALID_SMS_CODE);
        }
    }

    private function _checkPasswordAvailable($password)
    {
        if ($password &&
            (!preg_match(AccountBusinessConstant::COMMON_REGULAR_PASSWORD_FORMAT, $password, $pwdMatches) || !$pwdMatches)) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_PASSWORD_FORMAT_INVALID);
        }
    }

    private function _checkUserByMobile($mobile)
    {
        $user = $this->_userService->getByMobile($mobile);
        $user || ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_USER_NOT_EXISTS);

        return $user;
    }

    private function _checkPassword(User $user, $password)
    {
        $user->password == $password || ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_USER_ACCOUNT_PASSWORD_WRONG);
    }
}
