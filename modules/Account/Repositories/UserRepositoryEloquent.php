<?php

namespace Modules\Account\Repositories;

use Jiuyan\Common\Component\InFramework\Components\InThriftRepositoryBaseComponent;
use Jiuyan\Common\Component\InFramework\Components\JthriftServiceAgencyComponent;
use Jiuyan\Common\Component\InFramework\Components\RequestParamsComponent;
use Jiuyan\Common\Component\InFramework\Exceptions\ThriftResponseException;
use Jiuyan\Tools\Business\EmojiTool;
use Jiuyan\Tools\Business\ImageTools;
use Jthrift\Services\OpenplatformServiceIf;
use Jthrift\Services\UserInfoServiceIf;
use Modules\Account\Models\OpenPlatformUser;
use Modules\Account\Models\User;
use Modules\Account\Constants\AccountBusinessConstant;

/**
 * Class UserRepositoryEloquent
 * @package namespace Modules\Account\Repositories;
 */
class UserRepositoryEloquent extends InThriftRepositoryBaseComponent implements UserRepository
{
    /**
     * @var UserInfoServiceIf|JthriftServiceAgencyComponent
     */
    public $modelHandle = null;

    /**
     * @var OpenplatformServiceIf
     */
    public $openPlatformUserModelHandle = null;

    /**
     * special特殊的一些uc error code
     * @var array
     */
    public $specialExceptionCodes = [];

    public function __construct()
    {
        $this->modelHandle = app('UserInfoThriftService');
        $this->openPlatformUserModelHandle = app('OpenPlatformService');
    }

    /**
     * @param array $items
     * @return bool|User
     */
    protected function _convertCollection($items = [])
    {
        $items = $items ?: [];
        return new User($items);
    }

    /**
     * @param array $items
     * @return OpenPlatformUser
     */
    protected function _convertOpenPlatformUserCollection($items = [])
    {
        return new OpenPlatformUser($items);
    }

    private function _removeSensitiveFields(&$userInfo)
    {
        unset(
            $userInfo->password,
            $userInfo->access_token,
            $userInfo->token_get_time,
            $userInfo->expires_in,
            $userInfo->access_token_secret,
            $userInfo->is_robot,
            $userInfo->followers_count,
            $userInfo->friends_count,
            $userInfo->statuses_count,
            $userInfo->favourites_count,
            $userInfo->im_id,
            $userInfo->im_password,
            $userInfo->email,
            $userInfo->sina_email,
            $userInfo->auth_time,
            $userInfo->updated_at,
            $userInfo->created_at,
            $userInfo->cache_key
        );
    }

    private function _removeAuthFields(&$userInfo)
    {
        unset($userInfo->private_key);
        unset($userInfo->token);
    }

    private function _formatSomeField(&$userInfo)
    {
        if (isset($userInfo->name) && $userInfo->name) {
            $userInfo->name = EmojiTool::decode($userInfo->name);
        }
        if (isset($userInfo->desc) && $userInfo->desc) {
            $userInfo->desc = EmojiTool::decode($userInfo->desc);
        }
        if (strpos($userInfo->address, 'null') !== false) {
            $userInfo->address = preg_replace('/null|\(null\)/', '', $userInfo->address);
            $userInfo->address = trim($userInfo->address);
        }
        if (empty($userInfo->address)) {
            $userInfo->address = '中国';
        }
    }

    /**
     * @param $userInfo
     * @return bool
     */
    private function _formatUserInfoBase(User &$userInfo)
    {
        if ($userInfo->isEmpty()) {
            return false;
        }
        $this->_removeSensitiveFields($userInfo);
        $this->_formatUserAvatar($userInfo);
        $this->_formatSomeField($userInfo);
        return true;
    }

    private function _formatUserInfo(&$userInfo)
    {
        if (!$this->_formatUserInfoBase($userInfo)) {
            return false;
        }
        $this->_removeAuthFields($userInfo);
        return true;
    }

    private function _formatUserTaskStatus(&$userInfo)
    {
        $userTaskStatus = (isset($userInfo->task_status) && $userInfo->task_status) ?
            $userInfo->task_status : AccountBusinessConstant::COMMON_USER_TASK_STATUS_ONE;
        if (!in_array(
            $userTaskStatus,
            [
                AccountBusinessConstant::COMMON_USER_TASK_STATUS_INIT,
                AccountBusinessConstant::COMMON_USER_TASK_STATUS_ONE,
                AccountBusinessConstant::COMMON_USER_TASK_STATUS_TWO,
                AccountBusinessConstant::COMMON_USER_TASK_STATUS_THREE,
            ]
        )) {
            return false;
        }
        /**
         * 用二进制数的形式, 用以同时存储多个状态标识
         */
        $taskStatusRes = array_reverse(str_split(sprintf("%02d", decbin($userTaskStatus))));
        $userInfo->task_status_arr = [
            'auth_mobile' => (AccountBusinessConstant::COMMON_USER_TASK_STATUS_AUTH_MOBILE == $taskStatusRes[0]) ? true : false,
            'upload_contact' => (AccountBusinessConstant::COMMON_USER_TASK_STATUS_UPLOAD_CONTACT == $taskStatusRes[1]) ? true : false
        ];
    }

    private function _formatAuthFields(&$userInfo, $authType)
    {
        switch ($authType) {
            case AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_MOBILE_REGISTER:
                $userInfo->is_first = true;
                $userInfo->current_type = AccountBusinessConstant::COMMON_ACCOUNT_TYPE_MOBILE;
                break;
            case AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_NORMAL_LOGIN:
                $userInfo->is_first = false;
                break;
            case AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_NORMAL_THIRD_PARTY_LOGIN:
                $userInfo->is_first = false;
                break;
            case AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_NORMAL_THIRD_PARTY_REGISTER;
            case AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_PARTNER_THIRD_PARTY_REGISTER;
                $userInfo->is_first = true;
                break;
        }
    }

    private function _formatUserInfoForAuth(&$userInfo, $authType)
    {
        if (!$this->_formatUserInfoBase($userInfo)) {
            return false;
        }
        if (isset($userInfo->private_key)) {
            $userInfo->_token = $userInfo->private_key;
            unset($userInfo->private_key);
        }
        if (isset($userInfo->token)) {
            $userInfo->_auth = $userInfo->token;
            unset($userInfo->token);
        }
        if (isset($userInfo->in_verified)) {
            $userInfo->in_verified = ($userInfo->in_verified == 1);
        }
        /**
         * 追加当前账号对于第三方平台的绑定情况数据
         */
        $this->_appendThirdPartyBindStatus($userInfo);
        $this->_formatUserTaskStatus($userInfo);
        $this->_formatAuthFields($userInfo, $authType);
        return true;
    }

    private function _appendThirdPartyBindStatus(&$userInfo)
    {
        $userInfo->bind_weibo = $this->getThirdPartyAccountBindStatus($userInfo->id, AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_WEIBO);
    }

    private function _formatThirdPartyUserInfo(&$userInfo, $sourceType, $sourceUserId)
    {
        $userInfo->current_type = $sourceType;
        $userInfo->source_id = $sourceUserId;
        $userInfo->is_guide_publish = false;
        $userInfo->is_guide_phone = false;
    }

    private function _formatUserAvatar(&$userInfo)
    {
        if (isset($userInfo->avatar) && $userInfo->avatar && $userInfo->avatar != 'false') {
            if (strpos($userInfo->avatar, 'http') === false) {//用户上传过头像之后，处理大头像
                $avatar = $userInfo->avatar;
                $userInfo->avatar = ImageTools::formatImg($userInfo->server, $avatar, 'w50');
                $userInfo->avatar_large = (strpos($avatar, 'w50') !== false) ?
                    str_replace('w50', 'w180', $avatar) :
                    ImageTools::formatImg($userInfo->server, $avatar, 'w180');
            } elseif (strpos($userInfo->avatar, 'http') !== false &&
                strpos($userInfo->avatar, 'jiuyan.info') !== false &&
                strpos($userInfo->avatar, 'res.jiuyan.info') === false
            ) {
                $userInfo->avatar_large = str_replace('50x', '180x', $userInfo->avatar);
            } else {
                if ($userInfo->source == 1) {//只有sina的才有这种规则
                    $userInfo->avatar_large = strtr($userInfo->avatar, array('/50/' => '/180/'));
                } elseif ($userInfo->source == 2) {//只有tencent才有这种规则
                    $prefixImageUrl = $userInfo->avatar;
                    if (strpos($prefixImageUrl, 'qzapp.qlogo.cn') !== false) {
                        $userInfo->avatar = $prefixImageUrl . '/50';
                        $userInfo->avatar_large = $prefixImageUrl . '/100';
                    } elseif (strpos($prefixImageUrl, 'q.qlogo.cn') !== false) {
                        $userInfo->avatar = rtrim($prefixImageUrl, '/') . '/40';
                        $userInfo->avatar_large = rtrim($prefixImageUrl, '/') . '/100';
                    }
                } elseif ($userInfo->source == 3) {//微信
                    $prefixImageUrl = $userInfo->avatar;
                    if (false !== $lastSlashPos = strrpos($prefixImageUrl, '/')) {
                        $userInfo->avatar = substr($prefixImageUrl, 0, $lastSlashPos) . '/46';
                    }
                    $userInfo->avatar_large = $prefixImageUrl;
                } else {
                    list($userInfo->avatar, $userInfo->avatar_large) = $this->_getDefaultAvatar();
                }
            }
        } else {
            list($userInfo->avatar, $userInfo->avatar_large) = $this->_getDefaultAvatar();
        }
    }

    /**
     * 获取默认头像
     * @return array
     */
    private function _getDefaultAvatar()
    {
        $http = 'http';
        if (RequestParamsComponent::isSecure()) {
            $http = 'https';
        }
        $avatar = $http . '://res.jiuyan.info/in66v2/src/images/default_50x50.png';
        $avatar_large = $http . '://res.jiuyan.info/in66v2/src/images/default_180x180.png';
        return [$avatar, $avatar_large];
    }

    private function _formatSpecialAuthField(&$userInfo)
    {
        $userInfo->is_guide_publish = false;
    }


    private function _formatUcResponse(&$response)
    {
        if (isset($response['succ']) && $response['succ']) {
            return [$response['data'], $response['code']];
        }
        /**
         * 特殊的异常发生时，可能要进行一些特殊处理，可能不会直接以异常的形式返回
         */
        if (in_array($response['code'], $this->specialExceptionCodes)) {
            return [$response['data'], $response['code']];
        }
        throw new ThriftResponseException($response['code'], 0);
    }

    private function _formatSpecialLoginUser(&$loginUser, $loginStatus)
    {
        if ($loginStatus != AccountBusinessConstant::COMMON_UC_EXCEPTION_PASSWORD_NOT_SET) {
            return false;
        }
        $this->_formatUserAvatar($loginUser);
        $this->_formatSomeField($loginUser);
        $loginUser->userInfo = $loginUser->toArray();
        $thirdPartyAccountList = $loginUser->source_bind_info ?? [];
        $this->_formatThirdPartyBindDetails($thirdPartyAccountList);
        $loginUser->thirdPartyAccountList = $thirdPartyAccountList;
        return true;
    }

    private function _formatThirdPartyBindDetails(&$thirdPartyBindDetails)
    {
        foreach ($thirdPartyBindDetails as &$thirdPartyInfo) {
            $thirdPartyInfo['real_name'] = EmojiTool::decode($thirdPartyInfo['real_name']);
        }
    }

    private function _formatThriftResponse($response)
    {
        list($formatRes, $resStatus) = $this->_formatUcResponse($response);
        $formatRes = $this->_convertCollection($formatRes);
        return [$formatRes, $resStatus];
    }

    /**
     * @param $specialExceptionCodes
     */
    public function setSpecialUcExceptions($specialExceptionCodes)
    {
        if (!is_array($specialExceptionCodes)) {
            $this->specialExceptionCodes = [$specialExceptionCodes];
        } else {
            $this->specialExceptionCodes = $specialExceptionCodes;
        }
    }

    public function getUserByMobileForAuth($mobile, $authType)
    {
        $userInfo = $this->_convertCollection($this->modelHandle->getUserInfoByMobile($mobile));
        $this->_formatUserInfoForAuth($userInfo, $authType);
        return $userInfo;
    }

    /**
     * @param $userId
     * @return \Illuminate\Support\Collection|User
     */
    public function getUserById($userId)
    {
        return $this->_convertCollection($this->modelHandle->getUserInfoById($userId));
    }

    public function getUserByIdForAuth($userId)
    {
        $userInfo = $this->getUserById($userId);
        $this->_formatUserInfoForAuth($userInfo, AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_NORMAL_AUTH);
        return $userInfo;
    }

    /**
     * @param $mobile
     * @return \Illuminate\Support\Collection|User
     */
    public function getUserByMobile($mobile)
    {
        $user = $this->_convertCollection($this->modelHandle->getUserInfoByMobile($mobile));
        $this->_formatUserInfoForAuth($user, AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_MOBILE_CHANGE);
        return $user;
    }

    public function getUserByToken($token)
    {
        $user = $this->_convertCollection($this->modelHandle->getUserInfoByPrivateKey($token));
        $this->_formatUserInfoForAuth($user, AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_NORMAL_SEARCH);
        return $user;
    }

    public function getUserThirdPartyBindDetails($userId)
    {
        $thirdPartyDetails = $this->modelHandle->getThirdPartyAccountInfo($userId);
        $this->_formatThirdPartyBindDetails($thirdPartyDetails);
        return $thirdPartyDetails;
    }

    public function getThirdPartyAccountBindStatus($userId, $sourceTypeId)
    {
        $checkResult = $this->modelHandle->getThirdPartyAccountBindStatus($userId, $sourceTypeId);
        return $checkResult === false ? false : ($checkResult['status'] == 1);
    }

    public function isUserInNumberRevisable($userId)
    {
        return $this->modelHandle->checkNumberPermission($userId);
    }

    public function changeAccountPassword($userId, $oldPassword, $newPassword)
    {
        $this->modelHandle->setFormatResultCallback([$this, 'parent::formatThriftResponse']);
        list($changeResult, $setStatus) = $this->_formatThriftResponse(
            $this->modelHandle->changePassword($userId, $oldPassword, $newPassword)
        );
        if ($changeResult) {
            $changeResult->_auth = $changeResult->token;
            unset($changeResult->token);
        }
        return $changeResult;
    }

    public function setAccountPassword($userId, $password)
    {
        $this->modelHandle->setFormatResultCallback([$this, 'parent::formatThriftResponse']);
        list($setResult, $setStatus) = $this->_formatThriftResponse(
            $this->modelHandle->setPassword($userId, $password)
        );
        return $setResult;
    }

    public function resetAccountPassword($userId, $newPassword)
    {
        return $this->modelHandle->resetPassword($userId, $newPassword);
    }

    public function registerByMobile($existUserId, $mobile, $password)
    {
        $this->modelHandle->setFormatResultCallback([$this, 'parent::formatThriftResponse']);
        list($registeredUser, $registerStatus) = $this->_formatThriftResponse(
            $this->modelHandle->mobileRegister($existUserId, $mobile, $password, json_encode(RequestParamsComponent::getAllCommonParams()))
        );
        $this->_formatUserInfoForAuth($registeredUser, AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_MOBILE_REGISTER);
        $this->_formatSpecialAuthField($registeredUser);
        return $registeredUser;
    }

    public function loginCommonAccount($accountName, $password, $accountType)
    {
        $this->modelHandle->setFormatResultCallback([$this, 'parent::formatThriftResponse']);
        list($loginUser, $loginStatus) = $this->_formatThriftResponse(
            $this->modelHandle->authPasswordLogin($accountType, $accountName, $password, json_encode(RequestParamsComponent::getAllCommonParams()))
        );
        $loginUser->login_status = $loginStatus;
        $this->_formatSpecialLoginUser($loginUser, $loginStatus);
        $this->_formatUserInfoForAuth($loginUser, AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_NORMAL_LOGIN);
        return $loginUser;
    }

    public function loginPartnerCommonAccount($accountName, $password, $accountType)
    {
        $this->modelHandle->setFormatResultCallback([$this, 'parent::formatThriftResponse']);
        list($loginUser, $loginStatus) = $this->_formatThriftResponse(
            $this->modelHandle->authPasswordLoginByApp($accountType, $accountName, $password, 'CITY_ACTIVITY')
        );
        $loginUser->login_status = $loginStatus;
        $this->_formatUserInfoForAuth($loginUser, AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_PARTNER_COMMON_LOGIN);
        return $loginUser;
    }

    public function changeAccountMobile($existsUserId, $currentUserId, $mobile)
    {
        return $this->modelHandle->changeMobile($existsUserId, $currentUserId, $mobile);
    }

    /**
     * TODO 现在调用的uc登录方法，把传递的appCode写死成CITY_ACTIVITY了，后续可能需要扩展
     * @param $sourceType
     * @param $sourceUserId
     * @param $accountInfo
     * @return \Illuminate\Support\Collection|User
     */
    public function registerPartnerThirdPartyAccount($sourceType, $sourceUserId, $accountInfo)
    {
        $registerInfo = $this->_convertCollection(
            $this->modelHandle->thirdPartyAccountLoginAndRegisterByApp($sourceType, $sourceUserId, json_encode($accountInfo), 'CITY_ACTIVITY')
        );
        $this->_formatUserInfoForAuth($registerInfo, AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_PARTNER_THIRD_PARTY_REGISTER);
        return $registerInfo;
    }

    public function loginThirdPartyAccount($sourceType, $sourceUserId, $accountInfo)
    {
        $accountInfo = $this->modelHandle->thirdPartyAccountLogin(
            $sourceType,
            $sourceUserId,
            json_encode($accountInfo),
            json_encode(RequestParamsComponent::getAllCommonParams())
        );
        if ($accountInfo === false) {
            return false;
        }
        $loginInfo = $this->_convertCollection($accountInfo);
        $this->_formatUserInfoForAuth($loginInfo, AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_NORMAL_THIRD_PARTY_LOGIN);
        $this->_formatThirdPartyUserInfo($loginInfo, $sourceType, $sourceUserId);
        return $loginInfo;
    }

    public function registerThirdPartyAccount($sourceType, $sourceUserId, $accountInfo)
    {
        $registerRes = $this->modelHandle->thirdPartyAccountRegister(
            $sourceType,
            $sourceUserId,
            json_encode($accountInfo),
            json_encode(RequestParamsComponent::getAllCommonParams())
        );
        if ($registerRes === false) {
            return false;
        }
        $registerInfo = $this->_convertCollection($registerRes);
        $this->_formatUserInfoForAuth($registerInfo, AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_NORMAL_THIRD_PARTY_REGISTER);
        $this->_formatThirdPartyUserInfo($registerInfo, $sourceType, $sourceUserId);
        return $registerInfo;
    }

    public function bindThirdPartyAccount($currentUserId, $sourceType, $sourceUserId, $accountInfo)
    {
        $this->modelHandle->setFormatResultCallback([$this, 'parent::formatThriftResponse']);
        list($authUserInfo, $bindStatus) = $this->_formatThriftResponse(
            $this->modelHandle->thirdPartyAccountBind($currentUserId, $sourceType, $sourceUserId, json_encode($accountInfo))
        );
        $authUserInfo->bind_status = $bindStatus;
        $this->_formatUserInfoForAuth($authUserInfo, AccountBusinessConstant::COMMON_ACCOUNT_AUTH_TYPE_NORMAL_THIRD_PARTY_BIND);
        return $authUserInfo;
    }

    /**
     * @param $userId
     * @param $sourceType
     * @param $sourceUserId
     * @param $isSafeModeRequired
     * @param bool $needUpdate 是否执行特殊的方法更新登录信息
     * @return \Illuminate\Support\Collection|User
     */
    public function unbindThirdPartyAccount($userId, $sourceType, $sourceUserId, $isSafeModeRequired, $needUpdate = false)
    {
        $this->modelHandle->setFormatResultCallback([$this, 'parent::formatThriftResponse']);
        if ($needUpdate) {
            $unbindResult = $this->modelHandle->thirdPartyAccountUnbindAndUpdateToken($userId, $sourceType, $sourceUserId, $isSafeModeRequired);
        } else {
            $unbindResult = $this->modelHandle->thirdPartyAccountUnbind($userId, $sourceType, $sourceUserId, $isSafeModeRequired);
        }
        list($authUserInfo, $unbindStatus) = $this->_formatThriftResponse($unbindResult);
        if ($authUserInfo) {
            /**
             * 解绑接口只有部分信息返回，所以只将特定的字段转换一下即可
             */
            $authUserInfo->_auth = $authUserInfo->token;
            unset($authUserInfo->token);
        }
        return $authUserInfo;
    }

    public function getOpenPlatformUser($openId)
    {
        return $this->_convertOpenPlatformUserCollection($this->openPlatformUserModelHandle->getOauthUser($openId));
    }
}
