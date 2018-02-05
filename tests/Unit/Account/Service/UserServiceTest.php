<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/29
 * Time: 14:44
 */

namespace Tests\Unit\Account\Api\V100;

use Jiuyan\Tools\Business\EncryptTool;
use Modules\Account\Constants\AccountBusinessConstant;
use Modules\Account\Services\UserService;
use Tests\ServiceTestCase;

class UserServiceTest extends ServiceTestCase
{
    const USER_MOBILE = 18626987654;
    /**
     * @var UserService
     */
    public $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = app(UserService::class);
    }

    public function testGetUserByMobile()
    {
        $mobile = self::USER_MOBILE;
        $errorMobile = '18626991234';
        $res = $this->userService->getUserByMobile($mobile);
        $rules = [
            'address' => 'string',
            'authed' => 'integer',
            'avatar' => 'string',
            'city' => 'integer',
            'comment_status' => 'enum:enable|disable',
            'desc' => 'string',
            'gender' => 'enum:男|女|未知',
            'id' => 'integer',
            'in_verified' => 'boolean',
            'is_legal' => 'boolean',
            'level' => 'integer',
            'mobile' => 'integer',
            'name' => 'string',
            'number' => 'string',
            'password_set' => 'boolean',
            'province' => 'integer',
            'publish_status' => 'enum:enable|disable',
            'real_name' => 'string',
            'server' => 'integer',
            'source' => 'integer',
            'task_status' => 'integer',
            'verified_reason' => 'string',
            'avatar_large' => 'string',
            '_token' => 'string',
            '_auth' => 'string',
            'bind_weibo' => 'boolean',
            'task_status_arr' =>  [
                'auth_mobile' => 'boolean',
                'upload_contact' => 'boolean'
            ]
        ];
        if ($res->id) {
            $this->_testJsonResult($rules, $res->toArray());
        } else {
            $this->assertTrue(true);
        }
        return $res->toArray();
    }

    /**
     * @param $existUser
     * @depends testGetUserByMobile
     */
    public function testRegisterUser($existUser)
    {
        $existsUserId = isset($existUser['id']) ? EncryptTool::decryptId($existUser['id']) : 0;
        $mobile = self::USER_MOBILE;
        $password = 'jiuyan123';
        $res = $this->userService->registerByMobile($existsUserId, $mobile, $password);
        $rules = [
            'address' => 'string',
            'authed' => 'integer',
            'avatar' => 'string',
            'city' => 'integer',
            'comment_status' => 'enum:enable|disable',
            'desc' => 'string',
            'fans_count' => 'string',
            'gender' => 'enum:男|女|未知',
            'id' => 'integer',
            'in_verified' => 'boolean',
            'level' => 'integer',
            'mobile' => 'integer',
            'name' => 'string',
            'number' => 'string',
            'photo_count' => 'string',
            'province' => 'integer',
            'publish_status' => 'enum:enable|disable',
            'real_name' => 'string',
            'registered' => 'string',
            'server' => 'integer',
            'source' => 'integer',
            'source_id' => 'string',
            'task_status' => 'integer',
            'verified' => 'string',
            'verified_reason' => 'string',
            'verified_type' => 'string',
            'watch_count' => 'string',
            'avatar_large' => 'string',
            '_token' => 'string',
            '_auth' => 'string',
            'bind_weibo' => 'boolean',
            'task_status_arr' => [
                'auth_mobile' => 'boolean',
                'upload_contact' => 'boolean',
            ],
            'is_first' => 'boolean',
            'current_type' => 'integer',
            'is_guide_publish' => 'boolean'
        ];
        $this->_testJsonResult($rules, $res);
    }

    public function testCommonLogin()
    {
        $userName = 'huasheng001';
        $accountType = AccountBusinessConstant::COMMON_ACCOUNT_TYPE_FOR_UC_IN_NUMBER;
        $password = 'jiuyan321';
        $userName = self::USER_MOBILE;
        $accountType = AccountBusinessConstant::COMMON_ACCOUNT_TYPE_FOR_UC_MOBILE;
        $password = 'jiuyan123';

        $res = $this->userService->loginCommonAccount($userName, $password, $accountType);
        $rules = [
            "address" => "string",
            "authed" => "integer",
            "avatar" => "string",
            "city" => "integer",
            "comment_status" => "enum:enable|disable",
            "desc" => "string",
            "gender" => "enum:男|女|未知",
            "id" => "integer",
            "in_verified" => 'boolean',
            "is_legal" => 'boolean',
            "level" => "integer",
            "mobile" => "integer",
            "name" => "string",
            "number" => "string",
            "password_set" => 'boolean',
            "province" => "integer",
            "publish_status" => "enum:enable|disable",
            "real_name" => "string",
            "server" => "integer",
            "source" => "integer",
            "task_status" => "integer",
            "verified_reason" => "string",
            "login_status" => "integer",
            "avatar_large" => "string",
            "_token" => "string",
            "_auth" => "string",
            "bind_weibo" => 'boolean',
            "task_status_arr" => [
                "auth_mobile" => 'boolean',
                "upload_contact" => 'boolean',
            ],
            "is_first" => 'boolean'
        ];
        $this->_testJsonResult($rules, $res->toArray());
        return $res;
    }

    /**
     * @param $loginUser
     * @depends testGetUserByMobile
     */
    public function testChangePassword($loginUser)
    {
        $oldPassword = 'jiuyan123';
        $newPassword = 'jiuyan321';
        $res = $this->userService->changeAccountPassword(EncryptTool::decryptId($loginUser['id']), $oldPassword, $newPassword);
        $rules = [
            '_auth' => 'string'
        ];
        $this->_testJsonResult($rules, $res->toArray());
        $oldPassword = 'jiuyan321';
        $newPassword = 'jiuyan123';
        $res = $this->userService->changeAccountPassword(EncryptTool::decryptId($loginUser['id']), $oldPassword, $newPassword);
    }

    public function testResetPassword()
    {
        $res = $this->userService->resetAccountPassword(self::USER_MOBILE, 'jiuyan123');
        $this->assertTrue($res);
    }
}
