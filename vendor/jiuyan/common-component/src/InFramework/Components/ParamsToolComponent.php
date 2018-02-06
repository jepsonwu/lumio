<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/10
 * Time: 14:26
 */

namespace Jiuyan\Common\Component\InFramework\Components;

use Jiuyan\Common\Component\InFramework\Constants\InFrameworkConstant;

class ParamsToolComponent
{
    public function mobile($mobileVal)
    {
        $mobileVal = str_replace(['+', '-', '_', ' ', '(', ')'], '', trim($mobileVal));
        if (preg_match(InFrameworkConstant::COMMON_MOBILE_FORMAT_REGEX, $mobileVal)) {
            return $mobileVal;
        }
        return false;
    }
}
