<?php

namespace Modules\Account\Models;

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
 * @property int $created_at
 * @property string $updated_at
 *
 * Class User
 * @package Modules\Account\Models
 */
class User extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = "user";

    protected $fillable = [
        "id", "username", "avatar", "mobile", "gender", "password", "token", "created_at", "updated_at"
    ];

    public function toArray()
    {
        $result = parent::toArray();
        unset($result['password'], $result['token'], $result['updated_at']);

        return $result;
    }

    public static function whereMobile(Builder $builder, $mobile)
    {
        return $builder->where("mobile", $mobile);
    }
}

