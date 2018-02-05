<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/16
 * Time: 16:00
 */

namespace Modules\Account\Events\Register;

use Jiuyan\Common\Component\InFramework\Events\BaseEvent;

class ThirdPartyUnBindSuccessEvent extends BaseEvent
{
    protected $_generalParamRules = [
        'authUser' => 1,
        'currentUser' => 1,
        'thirdPartyFlag' => 1
    ];
}