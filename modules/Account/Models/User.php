<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;


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

    protected $fillable = [
        "id", "username", "avatar", "mobile", "gender", "password", "token", "created_at", "updated_at"
    ];

    public function toArray()
    {
        return [
            "id" => $this->id
        ];
    }

    public static function aa(){

    }
}

