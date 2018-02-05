<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Jiuyan\Tools\Business\EncryptTool;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @property string id
 * Class User
 * @package Modules\Account\Models
 */
class User extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
        'address', 'auth_time', 'authed', 'avatar', 'cache_key', 'city', 'comment_status', 'created_at', 'id', 'name', 'number',
        'password', 'password_set', 'desc', 'gender', 'im_id', 'im_password', 'is_legal', 'in_verified', 'level', 'mobile',
        'private_key', 'province', 'publish_status', 'real_name', 'registered', 'server', 'source', 'source_id', 'task_status',
        'token', 'updated_at', 'verified', 'verified_reason', 'verified_type', 'source_bind_info', 'fans_count', 'photo_count', 'watch_count'
    ];

    public function isEmpty()
    {
        return !isset($this->id);
    }

    public function toArray()
    {
        if (($result = parent::toArray()) && isset($result['id'])) {
            $result['id'] = EncryptTool::encryptId($result['id']);
        }
        return $result;
    }
}

