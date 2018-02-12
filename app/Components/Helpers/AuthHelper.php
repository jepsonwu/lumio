<?php

namespace App\Components\Helpers;

use Modules\Account\Models\User;
use Auth;

class AuthHelper
{
    /**
     * @return mixed|User
     */
    public static function user()
    {
        return Auth::guard()->user();
    }
}