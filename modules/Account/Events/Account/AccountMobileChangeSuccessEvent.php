<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/6
 * Time: 19:48
 */

namespace Modules\Account\Events\Account;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;

class AccountMobileChangeSuccessEvent extends BaseEvent
{
    protected $_generalParamRules = [
        'mobile' => 1,
        'currentUser' => 1
    ];
}