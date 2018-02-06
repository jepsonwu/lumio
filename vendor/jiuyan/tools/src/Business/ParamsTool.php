<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/9
 * Time: 16:56
 */

namespace Jiuyan\Tools\Business;

use Jiuyan\Tools\Constants\ToolsConstant;

class ParamsTool
{
    public function mobile($mobileVal)
    {
        $mobileVal = str_replace(['+', '-', '_', ' ', '(', ')'], '', trim($mobileVal));
        if (preg_match(ToolsConstant::COMMON_MOBILE_FORMAT_REGEX, $mobileVal)) {
            return $mobileVal;
        }
        return false;
    }
}

