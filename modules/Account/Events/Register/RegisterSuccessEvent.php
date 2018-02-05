<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/16
 * Time: 16:00
 */

namespace Modules\Account\Events\Register;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;

class RegisterSuccessEvent extends BaseEvent
{
    protected $_generalParamRules = [
        'currentUser' => 1,
        'existsUser' => 1,
        'mobile' => 1
    ];
}