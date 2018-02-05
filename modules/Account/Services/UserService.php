<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/17
 * Time: 14:45
 */

namespace Modules\Account\Services;

use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Exception;
use Modules\Account\Components\AccountQueueComponent;
use Modules\Account\Constants\AccountErrorConstant;
use Modules\Account\Exceptions\UserCenterException;
use Modules\Account\Repositories\UserRepository;
use Modules\Account\Constants\AccountBusinessConstant;
use Jiuyan\Tools\Business\EmojiTool;
use Log;

class UserService extends BaseService
{
    /**
     * @var UserRepository
     */
    protected $_userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->_userRepository = $userRepository;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function getUserByMobileForAuth($mobile, $authType)
    {
        $user = $this->_userRepository->getUserByMobileForAuth($mobile, $authType);
        return $user;
    }

    /**
     * @param $userId
     * @return \Modules\Account\Models\User
     */
    public function getUserById($userId)
    {
        $user = $this->_userRepository->getUserById($userId);
        return $user;
    }

    /**
     * @param $mobile
     * @return \Modules\Account\Models\User
     */
    public function getUserByMobile($mobile)
    {
        $user = $this->_userRepository->getUserByMobile($mobile);
        return $user;
    }

    public function getUserByToken($token)
    {
        $user = $this->_userRepository->getUserByToken($token);
        return $user;
    }

    /**
     * @param $existUserId
     * @param $mobile
     * @param $password
     * @return \Modules\Account\Models\User
     */
    public function registerByMobile($existUserId, $mobile, $password)
    {
        try {
            $registeredUser = $this->_userRepository->registerByMobile($existUserId, $mobile, $password);
            return $registeredUser;
        } catch (Exception $e) {
            $this->_respondUcException($e);
        }
    }

    public function registerByMobileFromWeb()
    {
    }

    public function loginCommonThirdParty($authInfo, $thirdPartyFlag)
    {
        $formatFuncName = '_format' . ucfirst($thirdPartyFlag) . 'AuthUserInfo';
        $loginUserInfo = $this->{$formatFuncName}($authInfo);
        return $this->_loginCommonThirdPartyAccount($loginUserInfo);
    }

    public function registerCommonThirdParty($authInfo, $thirdPartyFlag)
    {
        $formatFuncName = '_format' . ucfirst($thirdPartyFlag) . 'AuthUserInfo';
        $registerUserInfo = $this->{$formatFuncName}($authInfo);
        return $this->_registerCommonThirdPartyAccount($registerUserInfo);
    }

    public function bindCommonThirdParty($currentUserInfo, $authInfo, $thirdPartyFlag)
    {
        $formatFuncName = '_format' . ucfirst($thirdPartyFlag) . 'AuthUserInfo';
        $authUserInfo = $this->{$formatFuncName}($authInfo);
        return $this->_bindCommonThirdPartyAccount($currentUserInfo['id'], $authUserInfo);
    }

    /**
     * @param $unbindInfo
     * @param $isSafeModeRequired
     * @param bool $specialVersion 新版本且针对微信的解绑，需要调用特别的方法处理
     * @return mixed
     */
    public function unbindCommonThirdParty($unbindInfo, $isSafeModeRequired, $specialVersion = false)
    {
        try {
            return $this->_userRepository->unbindThirdPartyAccount(
                $unbindInfo['currentUserId'],
                $unbindInfo['sourceTypeId'],
                $unbindInfo['sourceUserId'],
                $isSafeModeRequired,
                $specialVersion
            );
        } catch (Exception $e) {
            $this->_respondUcException($e);
        }
    }

    public function loginCommonAccount($accountName, $password, $accountType)
    {
        try {
            $this->_userRepository->setSpecialUcExceptions(AccountBusinessConstant::COMMON_UC_EXCEPTION_PASSWORD_NOT_SET);
            return $this->_userRepository->loginCommonAccount($accountName, $password, $accountType);
        } catch (Exception $e) {
            Log::error('partner common login error code:' . $e->getCode() . ' msg:' . $e->getMessage());
            $this->_respondUcException($e);
        }
    }

    /**
     * 为内部partner提供的普通的账号密码登录
     * @param $accountName
     * @param $password
     * @param $accountType
     * @return mixed
     */
    public function loginPartnerCommonAccount($accountName, $password, $accountType)
    {
        try {
            return $this->_userRepository->loginPartnerCommonAccount($accountName, $password, $accountType);
        } catch (Exception $e) {
            Log::error('partner common login error code:' . $e->getCode() . ' msg:' . $e->getMessage());
            $this->_respondUcException($e);
        }
    }

    public function registerPartnerWeixin($authInfo)
    {
        $registerUserInfo = $this->_formatWeixinAuthUserInfo($authInfo);
        return $this->_registerPartnerThirdPartyAccount($registerUserInfo);
    }

    protected function _loginCommonThirdPartyAccount($accountInfo)
    {
        return $this->_userRepository->loginThirdPartyAccount($accountInfo['source'], $accountInfo['source_id'], $accountInfo);
    }

    protected function _registerCommonThirdPartyAccount($accountInfo)
    {
        return $this->_userRepository->registerThirdPartyAccount($accountInfo['source'], $accountInfo['source_id'], $accountInfo);
    }

    protected function _bindCommonThirdPartyAccount($currentUserId, $accountInfo)
    {
        try {
            $this->_userRepository->setSpecialUcExceptions(AccountBusinessConstant::COMMON_UC_EXCEPTION_SOURCE_ID_ALREADY_USED);
            return $this->_userRepository->bindThirdPartyAccount($currentUserId, $accountInfo['source'], $accountInfo['source_id'], $accountInfo);
        } catch (Exception $e) {
            Log::error('third party bind failed err:' . $e->getMessage());
            $this->_respondUcException($e);
        }
    }

    protected function _registerPartnerThirdPartyAccount($accountInfo)
    {
        return $this->_userRepository->registerPartnerThirdPartyAccount($accountInfo['source'], $accountInfo['source_id'], $accountInfo);
    }


    public function thirdPartyAccountLogin($accountInfo)
    {
        return $this->_userRepository->loginThirdPartyAccount($accountInfo['source'], $accountInfo['source_id'], $accountInfo);
    }

    public function getOpenPlatformUser($openId)
    {
        if (!$openPlatformUser = $this->_userRepository->getOpenPlatformUser($openId)) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_OPEN_PLATFORM_USER_GET_FAILED);
        }
        $userDetail = $this->_userRepository->getUserByIdForAuth($openPlatformUser->user_id);
        return $userDetail;
    }

    public function getUserThirdPartyBindDetails($userId)
    {
        return $this->_userRepository->getUserThirdPartyBindDetails($userId);
    }

    public function changeAccountPassword($userId, $oldPassword, $newPassword)
    {
        try {
            return $this->_userRepository->changeAccountPassword($userId, $oldPassword, $newPassword);
        } catch (Exception $e) {
            $this->_respondUcException($e);
        }
    }

    public function setAccountPassword($userId, $password)
    {
        try {
            return $this->_userRepository->setAccountPassword($userId, $password);
        } catch (Exception $e) {
            $this->_respondUcException($e);
        }
    }

    public function resetAccountPassword($accountMobile, $newPassword)
    {
        if (!$currentUser = $this->getUserByMobile($accountMobile)) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_USER_NOT_EXISTS);
        }
        if (($ret = $this->_userRepository->resetAccountPassword($currentUser->id, $newPassword) !== false)) {
            return true;
        }
        return false;
    }

    public function changeAccountMobile($existsUserId, $currentUserId, $mobile)
    {
         $ret = $this->_userRepository->changeAccountMobile($existsUserId, $currentUserId, $mobile);
         return $ret !== false ? true : false;
    }

    /**
     * 是否允许他人通过手机号搜索到我
     * @param $userId
     * @param $hiddenFlag
     */
    public function changeMobileHidden($userId, $hiddenFlag)
    {
        
    }

    public function updateUserSearchPool($updateFields)
    {
        $passKey = array(
            'number',
            'name',
            'avatar',
            'desc',
//            'photo_count',
//            'watch_count',
//            'fans_count',
            'address',
            'in_verified',
            'photo_count',
            'province',
            'publish_status',
            'comment_status',
            'city',
            'created_at'
        );
        if (array_intersect($passKey, array_keys($updateFields))) {
            AccountQueueComponent::userSearchPool($updateFields);
        }
    }

    /**
     * 判断in号是否可修改
     * @param $userId
     * @return bool
     */
    public function isUserInNumberRevisable($userId)
    {
        $status = $this->_userRepository->isUserInNumberRevisable($userId);
        /**
         * 由于对于thrift返回数据的格式化方法导致，判断in号是否可修改的逻辑要写成这样
         */
        return $status === false ? false : true;
    }

    private function _respondUcException(Exception $ucException)
    {
        $errorCode = $ucException->getMessage();
        switch ($errorCode) {
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_TYPE_ERROR:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_USER_LOGIN_TYPE_ERROR;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_PASSWORD_FORMAT_ERROR:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_PASSWORD_FORMAT_INVALID;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_PASSWORD_ALREADY_SET:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_PASSWORD_ALREADY_SET;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_MOBILE_USED_BY_ANOTHER_USER:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_PHONE_ALREADY_REGISTERED;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_OLD_PASSWORD_ERROR:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_USER_ACCOUNT_PASSWORD_WRONG;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_OLD_PASSWORD_FORMAT_ERROR:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_PASSWORD_FORMAT_INVALID;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_NEW_PASSWORD_FORMAT_ERROR:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_PASSWORD_FORMAT_INVALID;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_USER_NOT_EXIST:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_USER_NOT_EXISTS;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_ALREADY_BINDING:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_TYPE_OF_THIRD_PARTY_ALREADY_BIND;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_SOURCE_ID_ALREADY_USED:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_THIS_THIRD_PARTY_ALREADY_BIND;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_MOBILE_FORMAT_ERROR:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_USER_PHONE_FORMAT_ERROR;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_IN_NUMBER_FORMAT_ERROR:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_USER_NUMBER_FORMAT_ERROR;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_USER_AND_PASSWORD_NOT_MATCH:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_USER_ACCOUNT_PASSWORD_WRONG;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_NOT_BINDING:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_USER_ACCOUNT_BIND_EMPTY;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_YOU_SHOULD_SET_PASSWORD:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_USER_THIRD_PARTY_UNBIND_FOR_NO_PASSWORD;
                break;
            case AccountBusinessConstant::COMMON_UC_EXCEPTION_ACCOUNT_IS_IN_BLACK:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_REGISTER_FAILED;
                break;
            default:
                $commonExceptionTpl = AccountErrorConstant::ERR_ACCOUNT_UC_COMMON_EXCEPTION;
                break;
        }
        ExceptionResponseComponent::customize($commonExceptionTpl, UserCenterException::class);
    }

    private function _formatWeiboAuthUserInfo($authInfo)
    {
        $authUser = $authInfo->user;
        $authUserInfo = [
            'name' => $authUser['screen_name'],
            'real_name' => $authUser['name'],
            'gender' => $authUser['gender'],
            'avatar' => $authUser['profile_image_url'],
            'source' => AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_WEIBO,
            'source_id' => $authUser['id'],
            'followers_count' => $authUser['followers_count'],
            'friends_count' => $authUser['friends_count'],
            'statuses_count' => $authUser['statuses_count'],
            'favourites_count' => $authUser['favourites_count'],
            'verified' => intval($authUser['verified']),
            'verified_type' => $authUser['verified_type'],
            'verified_reason' => $authUser['verified_reason'],
            'province' => $authUser['province'],
            'city' => $authUser['city'],
            'address' => $authUser['location'],
            'desc' => $authUser['description'],
        ];
        $this->_appendAuthInitInfo($authInfo, $authUserInfo);
        $this->_formatCommonAuthUserInfo($authUserInfo);
        return $authUserInfo;
    }

    private function _formatQqAuthUserInfo($authInfo)
    {
        $authUser = $authInfo->userInfo;
        $avatar = (isset($authUser['figureurl']) && $authUser['figureurl']) ? $authUser['figureurl'] : $authUser['figureurl_qq_1'];//优先用空间头像
        $authUserInfo = [
            'name' => $authUser['nickname'],
            'real_name' => $authUser['nickname'],
            'source' => AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_QQ,
            'source_id' => $authInfo->openId,
            'gender' => $authUser['gender'] ? ($authUser['gender'] == '男' ? 'm' : 'f') : 'n',
            'avatar' => substr($avatar, 0, -3),
            'province' => 0,
            'city' => 0,
            'address' => '',
            'desc' => '',
        ];
        $this->_appendAuthInitInfo($authInfo, $authUserInfo);
        $this->_formatCommonAuthUserInfo($authUserInfo);
        return $authUserInfo;
    }

    private function _formatWeixinAuthUserInfo($authInfo)
    {
        $authUser = $authInfo->user;
        $authUserInfo = [
            'name' => $authUser['nickname'],
            'real_name' => $authUser['nickname'],
            'gender' => $authUser['sex'] ? ($authUser['sex'] == 1 ? 'm' : 'f') : 'n',
            'avatar' => $authUser['headimgurl'],
            'source' => AccountBusinessConstant::COMMON_THIRD_PARTY_SOURCE_WEIXIN,
            'source_id' => $authUser['unionid'],
            'province' => 0,
            'city' => 0,
            'address' => $authUser['province'] . ' ' . $authUser['city'],
            'desc' => ''
        ];
        $this->_appendAuthInitInfo($authInfo, $authUserInfo);
        $this->_formatCommonAuthUserInfo($authUserInfo);
        return $authUserInfo;
    }

    private function _appendAuthInitInfo($authInfo, &$authUserInfo)
    {
        $appendFields = [
            'email' => '',
            'publish_status' => 'enable',
            'comment_status' => 'enable',
            'authed' => 0,
            'mobile' => 0,
            'task_status' => 0,
            'access_token' => $authInfo->token,
            'expires_in' => $authInfo->expiresIn,
            'token_get_time' => time()
        ];
        $authUserInfo = array_merge($authUserInfo, $appendFields);
    }

    private function _formatCommonAuthUserInfo(&$authUserInfo)
    {
        //过滤null值
        foreach ($authUserInfo as $k => $v) {
            if (is_null($v)) {
                $authUserInfo[$k] = '';
            }
        }
        $gender = ['f' => '女', 'm' => '男', 'n' => '未知'];
        if (isset($authUserInfo['gender']) && isset($gender[$authUserInfo['gender']])) {
            $authUserInfo['gender'] = $gender[$authUserInfo['gender']];
        } else {
            $authUserInfo['gender'] = '未知';
        }
        if (isset($authUserInfo['name'])) {
            $authUserInfo['name'] = EmojiTool::encode($authUserInfo['name']);
        }
        if (isset($authUserInfo['real_name'])) {
            $authUserInfo['real_name'] = EmojiTool::encode($authUserInfo['real_name']);
        }
    }
}