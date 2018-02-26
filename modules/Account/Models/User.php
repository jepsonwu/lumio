<?php

namespace Modules\Account\Models;

use App\Components\BootstrapHelper\ErrorTrait;
use App\Components\BootstrapHelper\IModelAccess;
use App\Components\BootstrapHelper\ModelAccess;
use App\Constants\GlobalDBConstant;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $username
 * @property string $avatar
 * @property string $mobile
 * @property int $gender
 * @property string $qq
 * @property string $email
 * @property int $invited_user_id
 * @property string $invite_code
 * @property int $role
 * @property int $level
 * @property int $open_status
 * @property string $taobao_account
 * @property string $jd_account
 * @property string $password
 * @property string $token
 * @property string $token_expires
 * @property int $created_at
 * @property string $updated_at
 *
 * Class User
 * @package Modules\Account\Models
 */
class User extends Model implements Transformable, IModelAccess
{
    use ModelAccess;//todo 好像没啥用

    use ErrorTrait;

    protected $table = "user";

    const GENDER_FEMALE = 0;
    const GENDER_MALE = 1;
    const GENDER_UNKNOWN = 2;

    const ROLE_NORMAL = 0;
    const ROLE_BUYER = 1;
    const ROLE_SELLER = 2;

    protected $fillable = [
        "id", "username", "avatar", "mobile", "gender", "qq", "email", "invited_user_id", "invite_code", "role", "level",
        "open_status", "taobao_account", "jd_account", "password", "token", "token_expires", "created_at", "updated_at"
    ];

    public function transform()
    {
        $result = parent::toArray();

        unset($result['password'], $result['updated_at'], $result['invited_user_id'], $result['token_expires']);
        return $result;
    }

    public static function whereMobile(Builder $builder, $mobile)
    {
        return $builder->where("mobile", $mobile);
    }

    public static function whereToken(Builder $builder, $token)
    {
        return $builder->where("token", $token);
    }

    public static function whereRoleBuyer(Builder $builder)
    {
        return $builder->where("role", self::ROLE_BUYER);
    }

    public static function whereRoleSeller(Builder $builder)
    {
        return $builder->where("role", self::ROLE_SELLER);
    }

    public function changePassword($password)
    {
        return $this->update([
            "password" => $password
        ]);
    }

    public function isNormal()
    {
        return $this->role == self::ROLE_NORMAL;
    }

    public function isBuyer()
    {
        return $this->role == self::ROLE_BUYER;
    }

    public function isSeller()
    {
        return $this->role == self::ROLE_SELLER;
    }

    public function isAutoApplyTask()
    {
        return $this->open_status == GlobalDBConstant::DB_TRUE;
    }
}

