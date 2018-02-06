<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/27
 * Time: 16:27
 */

namespace Jiuyan\Tools;


class ConfigAutoload
{
    protected static $_config;

    public static function register()
    {
        if (!self::$_config) {
            $currentConfig = app('config')->get('tools', []);
            app('config')->set('tools', array_merge(require __DIR__ . '/../config/tools.php', $currentConfig));
        }
    }
}