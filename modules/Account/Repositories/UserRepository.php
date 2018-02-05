<?php

namespace Modules\Account\Repositories;

use Modules\Account\Models\User;

/**
 * Interface UserRepository
 * @package namespace Modules\Account\Repositories;
 */
interface UserRepository
{
    /**
     * @param $mobile
     * @param $authType
     * @return User
     */
    public function getUserByMobileForAuth($mobile, $authType);

    /**
     * @param $userId
     * @return User
     */
    public function getUserById($userId);

    public function getUserByIdForAuth($userId);

    /**
     * @param $mobile
     * @return User
     */
    public function getUserByMobile($mobile);

    public function getUserByToken($token);

    public function setSpecialUcExceptions($specialExceptionCodes);

    public function isUserInNumberRevisable($userId);
    public function setAccountPassword($userId, $password);
    public function changeAccountPassword($userId, $oldPassword, $newPassword);
    public function resetAccountPassword($userId, $newPassword);

    /**
     * @param $existUserId
     * @param $mobile
     * @param $password
     * @return User
     */
    public function registerByMobile($existUserId, $mobile, $password);
    public function changeAccountMobile($existsUserId, $currentUserId, $mobile);

    public function registerPartnerThirdPartyAccount($sourceType, $sourceUserId, $accountInfo);
    public function loginPartnerCommonAccount($accountName, $password, $accountType);
    public function loginCommonAccount($accountName, $password, $accountType);

    public function loginThirdPartyAccount($sourceType, $sourceUserId, $accountInfo);
    public function registerThirdPartyAccount($sourceType, $sourceUserId, $accountInfo);
    public function bindThirdPartyAccount($userId, $sourceType, $sourceUserId, $accountInfo);
    public function unbindThirdPartyAccount($userId, $sourceType, $sourceUserId, $isSafeModeRequired, $needUpdate = false);
    public function getUserThirdPartyBindDetails($userId);

    public function getOpenPlatformUser($openId);
}
