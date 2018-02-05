<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/1
 * Time: 下午3:21
 */

namespace Modules\User\Components\Extensions;

/**
 * @property string version
 * @property string language
 * @property string ip
 * @property string gps
 * @property string source
 * @property string deviceId
 * @property string network
 * @property string advertisingUnique
 * @property string advertisingChannel
 *
 */
class AppExtension extends AbstractExtension
{
    protected $propertyMap = [
        //application information
        "version" => "version",
        "language" => "language",

        //location information
        "ip" => "ip",
        "gps" => "gps",

        //device information
        "source" => "source",
        "deviceId" => "id",
        "network" => "network",

        //advertising information
        "advertisingUnique" => "advertising_unique",
        "advertisingChannel" => "advertising_channel",
    ];
}