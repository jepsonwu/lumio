<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/17
 * Time: 14:45
 */

namespace Modules\Account\Services;

use App\Constants\GlobalDBConstant;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Jiuyan\Tools\Business\EncryptTool;
use Modules\Account\Constants\AccountErrorConstant;
use Modules\Account\Models\User;
use Modules\Account\Repositories\UserRepositoryEloquent;
use Exception;

class UserService extends BaseService
{
    const TOKEN_EXPIRES = 864000;

    /**
     */
    protected $_userRepository;

    public function __construct(UserRepositoryEloquent $userRepository)
    {
        $this->_userRepository = $userRepository;
        $this->_requestParamsComponent = app('RequestCommonParams');
    }

    /**
     * @param $userId
     * @return \Modules\Account\Models\User
     */
    public function getById($userId)
    {
        $user = $this->_userRepository->find($userId);
        return $user;
    }

    /**
     * @param $userId
     * @return User
     * @throws \Jiuyan\Common\Component\InFramework\Exceptions\BusinessException
     */
    public function isValidById($userId)
    {
        $user = null;
        try {
            $user = $this->getById($userId);
        } catch (Exception $e) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_USER_NOT_EXISTS);
        }

        return $user;
    }

    /**
     * @param $mobile
     * @return \Illuminate\Database\Eloquent\Model|null|static|User
     */
    public function getByMobile($mobile)
    {
        return $this->_userRepository->getByMobile($mobile);
    }

    public function getUserByToken($token)
    {
        $user = $this->_userRepository->getByToken($token);
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
        return $this->_userRepository->create([
            "mobile" => $attributes['mobile'],
            "password" => $attributes['password'],
            "gender" => User::GENDER_UNKNOWN,
            "role" => User::ROLE_NORMAL,
            "open_status" => GlobalDBConstant::DB_FALSE,
            "invite_code" => EncryptTool::encryptId(time() . rand(10, 99)),
            "invited_user_id" => $attributes['invited_user_id'],
            "token" => $this->generateToken($attributes['mobile']),
            "token_expires" => time() + self::TOKEN_EXPIRES,
            "created_at" => time()
        ]);
    }

    /**
     * @param $userId
     * @param $attributes
     * @return mixed|User
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($userId, $attributes)
    {
        $user = $this->_userRepository->update([
            "username" => "",
            "avatar" => "",
            "gender" => "",
            "qq" => "",
            "email" => "",
            "open_status" => "",
            "taobao_account" => "",
            "jd_account" => ""
        ], $userId);

        //todo 升级role

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
        return $this->_userRepository->changePassword($user, $newPassword);
    }

    public function isBuyer(User $user)
    {
        return $this->_userRepository->isBuyer($user);
    }

    public function isSeller(User $user)
    {
        return $this->_userRepository->isSeller($user);
    }
}