<?php

namespace Modules\Account\Models;

use App\Components\BootstrapHelper\ErrorTrait;
use App\Components\BootstrapHelper\IModelAccess;
use App\Components\BootstrapHelper\ModelAccess;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $username
 * @property string $avatar
 * @property string $mobile
 * @property int $gender
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

    protected $fillable = [
        "id", "username", "avatar", "mobile", "gender", "password", "token", "token_expires", "created_at", "updated_at"
    ];

    public function transform()
    {
        $result = parent::toArray();

        unset($result['password'], $result['updated_at']);
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

    public function changePassword($password)
    {
        return $this->update([
            "password" => $password
        ]);
    }
}

