<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class OpenPlatformUser extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
        'user_id'
    ];
}

