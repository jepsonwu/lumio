<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/17
 * Time: 14:45
 */

namespace Modules\Account\Services;

use App\Constants\GlobalDBConstant;
use Illuminate\Support\Collection;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Jiuyan\Tools\Business\EncryptTool;
use Modules\Account\Constants\AccountErrorConstant;
use Modules\Account\Models\User;
use Modules\Account\Repositories\UserRepositoryEloquent;
use Exception;

/**
 * Class UserService
 * @mixin UserRepositoryEloquent
 * @package Modules\Account\Services
 */
class UserService extends BaseService
{
    const TOKEN_EXPIRES = 864000;

    protected $_allowCallRepositoryMethods = [
        "becomeSeller"
    ];

    public function __construct(UserRepositoryEloquent $userRepository)
    {
        $this->setRepository($userRepository);
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    public function list($conditions)
    {
        $result = $this->getRepository()->paginateWithWhere($conditions, 10);
        $result->each(function (User $user) {
            return $this->formatSecurity($user);
        });

        return $result;
    }

    /**
     * @param $userId
     * @return \Modules\Account\Models\User
     */
    public function getById($userId)
    {
        $user = $this->getRepository()->find($userId);
        return $user;
    }

    public function formatSecurity($userMix)
    {
        if (is_array($userMix) || $userMix instanceof Collection) {
            foreach ($userMix as &$user) {
                $user = $this->formatSecurityRaw($user);
            }
        } else {
            $userMix = $this->formatSecurityRaw($userMix);
        }

        return $userMix;
    }

    protected function formatSecurityRaw(User $user)
    {
        $user->token = "";
        return $user;
    }

    /**
     * @param $userId
     * @return User
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function isValidById($userId)
    {
        $user = $this->getById($userId);
        $user || ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_USER_NOT_EXISTS);
        return $user;
    }

    /**
     * @param $mobile
     * @return \Illuminate\Database\Eloquent\Model|null|static|User
     */
    public function getByMobile($mobile)
    {
        return $this->getRepository()->getByMobile($mobile);
    }

    public function getUserByToken($token)
    {
        $user = $this->getRepository()->getByToken($token);
        $user && $user->token_expires < time() && $user = [];

        return $user;
    }

    /**
     * @param $attributes
     * @return mixed|User
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create($attributes)
    {
        return $this->getRepository()->create([
            "mobile" => $attributes['mobile'],
            "password" => $attributes['password'],
            "gender" => User::GENDER_UNKNOWN,
            "role" => User::ROLE_NORMAL,
            "open_status" => GlobalDBConstant::DB_FALSE,
            "invite_code" => '',
            "invited_user_id" => $attributes['invited_user_id'],
            "token" => $this->generateToken($attributes['mobile']),
            "token_expires" => time() + self::TOKEN_EXPIRES,
            "taobao_account" => "",
            "jd_account" => "",
            "username" => "",
            "avatar" => "",
            "qq" => "",
            "email" => "",
            "level" => 1,
            "created_at" => time()
        ]);
    }

    /**
     * @param User $user
     * @param $attributes
     * @return mixed|User
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(User $user, $attributes)
    {
        $data = [
            "username" => array_get($attributes, "username", ""),
            "avatar" => array_get($attributes, "avatar", ""),
            "gender" => array_get($attributes, "gender", User::GENDER_UNKNOWN),
            "qq" => array_get($attributes, "qq", ""),
            "email" => array_get($attributes, "email", ""),
            "open_status" => array_get($attributes, "open_status", GlobalDBConstant::DB_TRUE),
            "taobao_account" => array_get($attributes, "taobao_account", ""),
            "jd_account" => array_get($attributes, "jd_account", "")
        ];

        switch (1) {
            case $user->isNormal():
                (!empty($data['taobao_account']) || !empty($data['jd_account']))
                && $data['role'] = User::ROLE_BUYER;
                break;
            case $user->isBuyer():
                (empty($data['taobao_account']) && empty($data['jd_account']))
                && $data['role'] = User::ROLE_NORMAL;
                break;
            case $user->isSeller():
                break;
        }

        $user = $this->getRepository()->update($data, $user->id);
        $user || ExceptionResponseComponent::business(AccountErrorConstant::ERR_USER_UPDATE_FAILED);

        return $user;
    }

    public function updateToken(User $user)
    {
        $user->token = $this->generateToken($user->mobile);
        $user->token_expires = time() + self::TOKEN_EXPIRES;

        return $user->update();
    }

    protected function generateToken($mobile)
    {
        return md5($mobile . rand(1, 99999999999) . microtime(true));
    }


    public function changePassword(User $user, $newPassword)
    {
        return $this->getRepository()->changePassword($user, $newPassword);
    }

    public function isBuyer(User $user)
    {
        return $this->getRepository()->isBuyer($user);
    }

    public function isSeller(User $user)
    {
        return $this->getRepository()->isSeller($user);
    }

    public function isDeployTaobaoAccount(User $user)
    {
        $user->taobao_account
        || ExceptionResponseComponent::business(AccountErrorConstant::ERR_USER_NO_DEPLOY_TAOBAO_ACCOUNT);

        return true;
    }

    public function isDeployJdAccount(User $user)
    {
        $user->jd_account
        || ExceptionResponseComponent::business(AccountErrorConstant::ERR_USER_NO_DEPLOY_JD_ACCOUNT);

        return true;
    }

    public function isAutoApplyTask(User $user)
    {
        return $user->isAutoApplyTask();
    }

    /**
     * @return mixed|UserRepositoryEloquent
     */
    public function getRepository()
    {
        return parent::getRepository();
    }
}