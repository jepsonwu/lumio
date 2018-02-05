<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/4
 * Time: 下午4:18
 */

namespace Modules\User\Components\Extensions;

/**
 * @property string totalDays
 * @property string accumulativeDays
 * @property string registerTime
 *
 * Class LoginExtension
 * @package Modules\User\Components\Extensions
 */
class LoginExtension extends AbstractExtension
{
    protected $propertyMap = [
        //login information
        "totalDays" => "login_total_days",
        "accumulativeDays" => "login_accumulative_days",
        "registerTime" => "register_time"
    ];

    public function getRegisterDays()
    {
        $registerDay = 0;

        if ($this->registerTime) {
            $diffTime = strtotime(date('Ymd')) - intval(strtotime(date('Ymd', $this->registerTime)));
            $registerDay = round($diffTime / 86400) + 1;
        }

        return $registerDay;
    }
}