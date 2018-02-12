<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/17
 * Time: 14:45
 */

namespace Modules\Account\Services;

use App\Constants\GlobalDBConstant;
use Jiuyan\Common\Component\InFramework\Services\BaseService;
use Jiuyan\Tools\Business\EncryptTool;
use Modules\Account\Constants\AccountBanyanDBConstant;
use Modules\Account\Models\User;
use Modules\Account\Repositories\UserRepositoryEloquent;

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
}