<?php

namespace App\Components\Helpers;

use Modules\Account\Models\User;

class AuthHelper
{
    /**
     * @return mixed|User
     */
    public static function user()
    {
        return \Auth::user();
    }
}