<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/16
 * Time: 16:03
 */

namespace Modules\Account\Events\Register;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;

class RegisterFailedEvent extends BaseEvent
{
    protected $_generalParamRules = [
        'mobile' => 1
    ];
}