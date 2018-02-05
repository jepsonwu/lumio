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

    protected $_userProfileService;

    public function __construct(AccountRepository $repository, UserService $userService, UserProfileService $userProfileService)
    {
        $this->_repository = $repository;
        $this->_userService = $userService;
        $this->_userProfileService = $userProfileService;
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

        $forceRegisterFlag = $this->_checkSpecialForceProcess($requestParams);
        $existsUser = $this->_userService->getUserByMobileForAuth($requestParams['mobile'], AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_MOBILE_REGISTER);
        if (!$forceRegisterFlag && !$existsUser->isEmpty()) {
            $this->triggerEvent(AccountEventConstant::REGISTER_REPEAT, $requestParams);
            $this->_appendExtendFields($existsUser, AccountBusinessConstant::COMMON_ACCOUNT_TYPE_FOR_UC_MOBILE);
            return $this->getServiceResponse($existsUser, AccountErrorConstant::ERR_ACCOUNT_PHONE_ALREADY_REGISTERED);
        } elseif ($forceRegisterFlag && $existsUser->isEmpty()) {
            $this->triggerEvent(AccountEventConstant::REGISTER_FORCE_FAILED, $requestParams);
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_REGISTER_FAILED);
        } else {
            $existsUserId = $existsUser->id ?? 0;
            if ($registerUser = $this->_userService->registerByMobile($existsUserId, $requestParams['mobile'], $requestParams['password'])) {
                $requestParams['currentUser'] = $registerUser;
                $requestParams['existsUser'] = $existsUser;
                $this->_finishSmsCaptcha($requestParams);
                $this->triggerEvent(AccountEventConstant::REGISTER_SUCCESS, $requestParams);
                $this->_appendExtendFields($registerUser, AccountBusinessConstant::COMMON_ACCOUNT_TYPE_FOR_UC_MOBILE);
                return $this->getServiceResponse($registerUser, AccountResponseCodeConstant::COMMON_REGISTER_SUCCESS);
            }
            $this->triggerEvent(AccountEventConstant::REGISTER_FAILED, $requestParams);
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_REGISTER_FAILED);
        }
    }

    public function loginCommonAccount($requestParams)
    {
        $userName = $requestParams['username'];
        list($userName, $accountType) = $this->_formatUserNameType($userName);
        $loginUser = $this->_userService->loginCommonAccount($userName, $requestParams['password'], $accountType);
        $this->_appendExtendFields($loginUser, $accountType);
        /**
         * 如果uc报了一些特殊的异常，会触发一些特殊的响应内容
         */
        list($response, $codeTpl) = $this->_respondForSpecialUcException($loginUser);
        if ($codeTpl) {
            return $this->getServiceResponse($response, $codeTpl, false);
        } else {
            return $this->getServiceResponse($loginUser, $codeTpl);
        }
    }

    public function loginPartnerCommonAccount($requestParams, $partnerFlag)
    {
        $userName = $requestParams['user_name'];
        $password = $requestParams['password'];
        list($userName, $accountType) = $this->_formatUserNameType($userName);
        $loginUser = $this->_userService->loginPartnerCommonAccount($userName, $password, $accountType);
        return $loginUser;
    }

    public function loginPartnerInAccount($requestParams, $partnerFlag)
    {
        $authUser = $this->_getInAuthUserInfo($requestParams);
        return $authUser;
    }

    public function registerPartnerWeixin($requestParams, $partnerFlag)
    {
        $authInfo = $this->getAuthInfo('weixin', $requestParams);
        return $this->_userService->registerPartnerWeixin($authInfo);
    }

    public function authThirdParty($requestParams)
    {
        $thirdPartyFlag = $requestParams['thirdPartyFlag'];
        $authInfo = $this->getAuthInfo($thirdPartyFlag, $requestParams);
        /**
         * 基于UC目前对于登录注册流程的处理现状：
         * 每次需要先进行一次登录，如果没有返回uid，则再进行一次注册。
         */
        if (!($loginUser = $this->_userService->loginCommonThirdParty($authInfo, $thirdPartyFlag))) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_THIRD_PARTY_LOGIN_FAILED);
        }
        if (isset($loginUser->id) && $loginUser->id) {
            $requestParams['authUser'] = $loginUser;
            $this->triggerEvent(AccountEventConstant::THIRD_PARTY_LOGIN_SUCCESS, $requestParams);
            return $loginUser;
        } else {
            if ($registerUser = $this->_userService->registerCommonThirdParty($authInfo, $thirdPartyFlag)) {
                $requestParams['authUser'] = $registerUser;
                $this->triggerEvent(AccountEventConstant::THIRD_PARTY_REGISTER_SUCCESS, $requestParams);
                return $registerUser;
            }
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_THIRD_PARTY_LOGIN_FAILED);
        }
    }

    public function bindThirdParty($requestParams)
    {
        try {
            $thirdPartyFlag = $this->_formatThirdPartyFlag($requestParams['type']);
            $currentUser = $requestParams['currentUser'];
            $authInfo = $this->getAuthInfo($thirdPartyFlag, $requestParams);
            $bindUserInfo = $this->_userService->bindCommonThirdParty($currentUser, $authInfo, $thirdPartyFlag);
            /**
             * 绑定时，有些情况下，即便返回的是错误状态，也需要把授权信息返回
             */
            $bindStatus = $bindUserInfo->bind_status;
            $responseTpl = '';
            if ($bindStatus) {
                $responseTpl = AccountErrorConstant::ERR_ACCOUNT_THIS_THIRD_PARTY_ALREADY_BIND;
                return $this->getServiceResponse($bindUserInfo, $responseTpl, false);
            } else {
                /**
                 * 兼容旧的接口逻辑，增加了这么个字段
                 * 不知道客户端用来干嘛的
                 */
                $bindUserInfo->waiting_time = '0';
                $requestParams['authUser'] = $bindUserInfo;
                $requestParams['thirdPartyFlag'] = $thirdPartyFlag;
                $this->triggerEvent(AccountEventConstant::THIRD_PARTY_BIND_SUCCESS, $requestParams);
                return $this->getServiceResponse($bindUserInfo, $responseTpl);
            }
        } catch (Exception $e) {
            $this->_respondForUcException($e, AccountErrorConstant::ERR_ACCOUNT_THIRD_PARTY_BIND_FAILED);
        }
    }

    public function unbindThirdParty($requestParams)
    {
        try {
            $requestParams['thirdPartyFlag'] = $this->_formatThirdPartyFlag($requestParams['thirdPartyTypeId']);
            list($unbindInfo, $isSafeModeRequired) = $this->_formatUnbindInfo($requestParams);
            if ($unbindResult = $this->_userService->unbindCommonThirdParty($unbindInfo, $isSafeModeRequired, $requestParams['specialVersion'])) {
                $this->triggerEvent(AccountEventConstant::THIRD_PARTY_UNBIND_SUCCESS, $requestParams);
            }
            return $unbindResult;
        } catch (Exception $e) {
            $this->_respondForUcException($e, AccountErrorConstant::ERR_ACCOUNT_USER_THIRD_PARTY_UNBIND_FAILED);
        }
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

    public function changeMobile($requestParams)
    {
        $currentMobile = $requestParams['mobile'];
        $currentUser = $requestParams['currentUser'];
        if ($currentUser['authed'] && $currentUser['mobile'] == $currentMobile) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_MOBILE_CHANGE_FAILED_FOR_SAME);
        }
        $bindResult = $this->_dealMobileChange($requestParams, AccountErrorConstant::ERR_ACCOUNT_USER_CHANGE_MOBILE_FAILED);
        if (true === $bindResult) {
            $this->triggerEvent(AccountEventConstant::ACCOUNT_MOBILE_CHANGE_SUCCESS, $requestParams);
            return $this->getServiceResponse([], AccountResponseCodeConstant::COMMON_ACCOUNT_MOBILE_CHANGE_SUCCESS);
        }
        return $bindResult;
    }

    public function bindMobile($requestParams)
    {
        $currentUser = $requestParams['currentUser'];
        $password = $requestParams['password'] ?? '';
        $wannerBeFound = $requestParams['show_mobile'] ?? false;
        $bindResult = $this->_dealMobileChange($requestParams, AccountErrorConstant::ERR_ACCOUNT_USER_CHANGE_MOBILE_FAILED);
        if (true === $bindResult) {
            if ($password) {
                $this->_userService->setAccountPassword($currentUser['id'], $password);
            }
            $this->_userProfileService->changeMobileSearchStatus($currentUser->id, $wannerBeFound);
            $this->triggerEvent(AccountEventConstant::ACCOUNT_MOBILE_BIND_SUCCESS, $requestParams);
            return $this->getServiceResponse([], AccountResponseCodeConstant::COMMON_ACCOUNT_MOBILE_BIND_SUCCESS);
        }
        return $bindResult;
    }

    public function getAccountSafetyCondition($requestParams)
    {
        $currentUser = $requestParams['currentUser'];
        $conditionDetail = [];
        $conditionDetail['thirdparty'] = $this->_userService->getUserThirdPartyBindDetails($currentUser['id']) ?: [];
        $conditionDetail['mobile'] = $currentUser['authed'] ? $currentUser['mobile'] : '0';
        $conditionDetail['password_set'] = $currentUser['password_set'];
        $conditionDetail['number_editable'] = $this->_userService->isUserInNumberRevisable($currentUser['id']);
        $this->_calculateUserSateLevel($conditionDetail, $currentUser);
        return $conditionDetail;
    }

    private function _formatThirdPartyFlag($thirdPartyType)
    {
        $thirdPartyFlag = '';
        switch ($thirdPartyType) {
            case AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_WEIBO:
                $thirdPartyFlag = AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_WEIBO;
                break;
            case AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_QQ:
                $thirdPartyFlag = AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_QQ;
                break;
            case AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_WEIXIN:
                $thirdPartyFlag = AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_WEIXIN;
                break;
            default:
                ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_USER_LOGIN_TYPE_ERROR);
                break;
        }
        return $thirdPartyFlag;
    }

    private function _dealMobileChange(&$requestParams, $exceptionTpl)
    {
        $forceOpe = $this->_checkSpecialForceProcess($requestParams);
        $requestParams['forceOpe'] = $forceOpe;
        $password = $requestParams['password'] ?? '';
        $this->_checkSmsCaptcha($requestParams['mobile'], $requestParams);
        $this->_checkPasswordAvailable($password);
        $existsUser = $this->_userService->getUserByMobile($requestParams['mobile']);
        if (!$forceOpe && !$existsUser->isEmpty()) {
            $this->_finishSmsCaptcha($requestParams, false);
            return $this->getServiceResponse($existsUser, AccountErrorConstant::ERR_ACCOUNT_PHONE_ALREADY_REGISTERED);
        } elseif ((!$forceOpe && $existsUser->isEmpty()) || ($forceOpe && !$existsUser->isEmpty())) {
            $existsUserId = $existsUser->id ?? 0;
            $currentUser = $requestParams['currentUser'];
            if ($this->_userService->changeAccountMobile($existsUserId, $currentUser['id'], $requestParams['mobile'])) {
                $this->_finishSmsCaptcha($requestParams);
                $requestParams['existsUser'] = $existsUser;
                return true;
            }
        }
        ExceptionResponseComponent::business($exceptionTpl);
    }

    private function _checkResetActionPermission($mobile)
    {
        $cacheHandle = AccountBanyanDBConstant::accountPasswordResetTimes();
        $resetTimes = $cacheHandle->get($mobile);
        if ($resetTimes) {
            if ($resetTimes >= AccountBusinessConstant::COMMON_ACCOUNT_PASSWORD_RESET_TIMES_LIMIT_PER_DAY) {
                Log::error('password-reset is too frequency mobile:' . $mobile  .' times:' . $resetTimes);
                ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_PASSWORD_RESET_FAILED);
            } else {
                $cacheHandle->inc($mobile, 1);
            }
        } else {
            $cacheHandle->set($mobile, 1, 86400);
        }
    }

    private function _calculateUserSateLevel(&$conditionDetail, &$currentUser)
    {
        $safeLevel = 1;
        if ($currentUser['password_set']) {
            $safeLevel++;
        }
        if ($currentUser['authed']) {
            $safeLevel += 2;
        }
        $safeLevel += count($conditionDetail['thirdparty']);
        $conditionDetail['safe_lvl'] = $safeLevel;
    }

    private function _appendExtendFields(&$authUser, $accountType)
    {
        switch ($accountType) {
            case AccountBusinessConstant::COMMON_ACCOUNT_TYPE_FOR_UC_MOBILE:
                $authUser->current_type = AccountBusinessConstant::COMMON_ACCOUNT_TYPE_MOBILE;
                break;
            case AccountBusinessConstant::COMMON_ACCOUNT_TYPE_FOR_UC_IN_NUMBER:
                $authUser->current_type = AccountBusinessConstant::COMMON_ACCOUNT_TYPE_IN_NUMBER;
                break;
        }
    }

    private function _respondForSpecialUcException(&$authUser)
    {
        $responseData = [];
        $responseCodeTpl = '';
        $loginStatus = $authUser->login_status;
        if ($loginStatus == AccountBusinessConstant::COMMON_UC_EXCEPTION_PASSWORD_NOT_SET) {
            $responseData['user_info'] = $authUser->userInfo;
            $responseData['thirdparty'] = $authUser->thirdPartyAccountList;
            $responseCodeTpl = AccountErrorConstant::ERR_ACCOUNT_USER_PASSWORD_NOT_SET;
        }
        return [$responseData, $responseCodeTpl];
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

    private function _formatUnbindInfo(&$requestParams)
    {
        $currentUser = $requestParams['currentUser'];
        $unbindInfo = [
            'currentUserId' => $requestParams['currentUser']['id'],
            'sourceTypeId' => $requestParams['thirdPartyTypeId'],
            'sourceUserId' => $requestParams['thirdPartyUserId'],
        ];
        /**
         * 是否需要采用安全的方式进行解绑：
         *  如果当前in号，只绑定了一个第三方账号，同时又没有设置登录密码，那解绑该第三方账号后，in号就无法登录了。
         *  因此，这里指定了一下，是否要采用安全方式进行操作：1-不需要(直接解除绑定) 0-需要（即如果满足上述条件，会提示用户该问题）
         *  如下：如果设置了密码，则不需要考虑这个问题；如果用户选择强制解绑也不需要考虑
         */
        $isSafeModeRequired = $currentUser['password_set'] ? 1 : ($requestParams['forceUnbind'] ?? 0);
        return [$unbindInfo, $isSafeModeRequired];
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

    private function _getInAuthUserInfo($requestParams)
    {
        if (!$authInfo = Socialite::driver('in')->getAccessTokenResponse($requestParams['code'])) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_THIRD_PARTY_AUTH_INFO_GET_FAILED);
        }
        $authUser = $this->_userService->getOpenPlatformUser($authInfo['open_id']);
        return $authUser;
    }

    public function getAuthInfo($authType, $requestParams)
    {
        /**
         * qq授权登录时，客户端会将用户信息直接获取到传递过来
         */
        if ($authType == AccountBusinessConstant::COMMON_THIRD_PARTY_FLAG_QQ) {
            $authInfo = new LumioCollection([
                'token' => $requestParams['access_token'],
                'expiresIn' => $requestParams['expires_in'],
                'openId' => $requestParams['open_id'],
                'userInfo' => json_decode($requestParams['user_info'], true)
            ]);
            return $authInfo;
        }
        if (!$authInfo = Socialite::driver($authType)->userFromToken($requestParams['access_token'])) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_THIRD_PARTY_AUTH_INFO_GET_FAILED);
        }
        $authInfo->setExpiresIn($requestParams['expires_in']);
        return $authInfo;
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
