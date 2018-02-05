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


    private function _respondUcException(Exception $ucException)
    {
        ExceptionResponseComponent::customize($commonExceptionTpl, UserCenterException::class);
    }
}