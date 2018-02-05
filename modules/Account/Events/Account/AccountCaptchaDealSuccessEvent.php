<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/6
 * Time: 15:03
 */

namespace Modules\Account\Events\Account;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;

class AccountCaptchaDealSuccessEvent extends BaseEvent
{
    protected $_generalParamRules = [
        'mobile' => 1
    ];
}