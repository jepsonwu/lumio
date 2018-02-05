<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/18
 * Time: 15:15
 */

namespace Modules\Account\Events\Register;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;

class RegisterRepeatEvent extends BaseEvent
{
    protected $_generalParamRules = [
        'mobile' => 1
    ];
}