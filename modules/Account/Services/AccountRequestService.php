<?php

namespace Modules\Account\Services;

use Barryvdh\Reflection\DocBlock\Tag\ParamTag;
use Jiuyan\Captcha\CaptchaComponent;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Exceptions\BusinessException;
use Jiuyan\Common\Component\InFramework\Libraries\System\LumioCollection;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Laravel\Socialite\Facades\Socialite;
use Modules\Account\Constants\AccountBanyanDBConstant;
use Modules\Account\Constants\AccountBusinessConstant;
use Modules\Account\Constants\AccountErrorConstant;
use Modules\Account\Constants\AccountEventConstant;
use Modules\Account\Constants\AccountResponseCodeConstant;
use Modules\Account\Repositories\AccountRepository;
use ParamsTool;
use Exception;
use Log;

class AccountRequestService extends BaseService
{
    /**
     * @var AccountRepository
     */
    protected $_repository;

    /**
     * @var UserService
     */
    protected $_userService;

    public function __construct(AccountRepository $repository, UserService $userService)
    {
        $this->_repository = $repository;
        $this->_userService = $userService;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function sendAccountCaptcha($requestParams)
    {
        $accountMobile = $requestParams['mobile'];
        if (!ParamsTool::mobile($accountMobile)) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_USER_PHONE_FORMAT_ERROR);
        }
        return CaptchaComponent::getInstance()->sendCaptcha($requestParams['mobile'], '', $requestParams['captcha_type']);
    }

    public function registerUser($requestParams)
    {
        $this->_checkPasswordAvailable($requestParams['password']);
        $this->_checkSmsCaptcha($requestParams['mobile'], $requestParams);

        //ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_REGISTER_FAILED);

        $forceRegisterFlag = $this->_checkSpecialForceProcess($requestParams);
        //return $this->getServiceResponse($loginUser, $codeTpl);
    }

    public function loginCommonAccount($requestParams)
    {
        $userName = $requestParams['username'];
        list($userName, $accountType) = $this->_formatUserNameType($userName);
        $loginUser = $this->_userService->loginCommonAccount($userName, $requestParams['password'], $accountType);
    }


    public function changeAccountPassword($requestParams)
    {
        try {
            $currentUserId = $requestParams['currentUser']['id'];
            $password = $requestParams['password'];
            $newPassword = $requestParams['new_password'];
            return $this->_userService->changeAccountPassword($currentUserId, $password, $newPassword);
        } catch (Exception $e) {
            $this->_respondForUcException($e, AccountErrorConstant::ERR_ACCOUNT_PASSWORD_SET_FAILED);
        }
    }

    public function setAccountPassword($requestParams)
    {
        try {
            $currentUserId = $requestParams['currentUser']['id'];
            $password = $requestParams['password'];
            $this->_checkPasswordAvailable($password);
            return $this->_userService->setAccountPassword($currentUserId, $password);
        } catch (Exception $e) {
            $this->_respondForUcException($e, AccountErrorConstant::ERR_ACCOUNT_PASSWORD_SET_FAILED);
        }
    }

    public function resetAccountPassword($requestParams)
    {
        $currentMobile = $requestParams['mobile'];
        $newPassword = $requestParams['password'];
        $this->_checkResetActionPermission($currentMobile);
        $this->_checkPasswordAvailable($newPassword);
        $this->_checkSmsCaptcha($currentMobile, $requestParams);

        if ($this->_userService->resetAccountPassword($currentMobile, $newPassword)) {
            $this->_finishSmsCaptcha($requestParams);
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

    /**
     * 对userService抛出的一些异常进行捕获，对于某些异常内容要进行转换
     * @param $exception
     * @param $defaultResponseTpl
     */
    private function _respondForUcException($exception, $defaultResponseTpl)
    {
        $exceptionTpl = ExceptionResponseComponent::convert($exception);
        if ($exceptionTpl == AccountErrorConstant::ERR_ACCOUNT_UC_COMMON_EXCEPTION) {
            ExceptionResponseComponent::business($defaultResponseTpl);
        } else {
            throw $exception;
        }
    }


    private function _formatUserNameType($userName)
    {
        if ($mobileResult = ParamsTool::mobile($userName)) {
            return [$mobileResult, AccountBusinessConstant::COMMON_ACCOUNT_TYPE_FOR_UC_MOBILE];
        } elseif (preg_match(AccountBusinessConstant::COMMON_REGULAR_IN_NUMBER_FORMAT, $userName)) {
            return [$userName, AccountBusinessConstant::COMMON_ACCOUNT_TYPE_FOR_UC_IN_NUMBER];
        }
        ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_USER_NAME_FORMAT_ERROR);
    }


    private function _finishSmsCaptcha(&$requestParams, $businessStatus = true)
    {
        if ($businessStatus) {
            $this->triggerEvent(AccountEventConstant::ACCOUNT_CAPTCHA_DEAL_SUCCESS, $requestParams);
        } else {
            $this->triggerEvent(AccountEventConstant::ACCOUNT_CAPTCHA_DEAL_FAILED, $requestParams);
        }
    }

    private function _checkSmsCaptcha($mobile, &$requestParams)
    {
        /**
         * 有时可能不需要进行验证码校验
         */
        $isCaptchaNeeded = $requestParams['needCode'] ?? 1;
        if (!$isCaptchaNeeded) {
            return true;
        }
        $captcha = $requestParams['code'] ?? '';
        $this->triggerEvent(AccountEventConstant::ACCOUNT_CAPTCHA_CHECK_START, $requestParams);
        if (!CaptchaComponent::getInstance()->verifyCaptcha($captcha, $mobile)) {
            $this->triggerEvent(AccountEventConstant::ACCOUNT_CAPTCHA_CHECK_FAILED, $requestParams);
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_INVALID_SMS_CODE);
        }
        $this->triggerEvent(AccountEventConstant::ACCOUNT_CAPTCHA_CHECK_SUCCESS, $requestParams);
        return true;
    }

    private function _checkPasswordAvailable($password)
    {
        if ($password &&
            (!preg_match(AccountBusinessConstant::COMMON_REGULAR_PASSWORD_FORMAT, $password, $pwdMatches) || !$pwdMatches)) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_VOICE_CODE_SEND_FAILED);
        }
    }

    /**
     * 对于使用手机号注册或绑定的流程中，都存在一个允许用户进行强制操作的逻辑
     * 这里则判断用户是否是请求强制流程
     * @param $requestParams
     * @return bool
     * @throws BusinessException
     */
    private function _checkSpecialForceProcess(&$requestParams)
    {
        $forceFlag = $requestParams['force'] ?? false;
        if ($forceFlag) {
            /**
             * TODO::用缓存进行基本的锁操作，防止操作过于频繁
             */
            if (false) {
                ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_FORCE_REGISTER_TOO_FREQUENTLY);
            }
            return true;
        }
        return false;
    }
}
