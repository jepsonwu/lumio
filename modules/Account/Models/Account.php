<?php

namespace Modules\Account\Models;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;

class Account extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];
}

