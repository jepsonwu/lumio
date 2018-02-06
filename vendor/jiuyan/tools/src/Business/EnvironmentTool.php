<?php
/**
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/5/18
 * Time: 14:44
 */

namespace Jiuyan\Tools\Business;

class EnvironmentTool
{

    /**
     * only online
     * @return bool
     */
    public static function isOnline()
    {
        return app()->environment() == "production";
    }

    /**
     * webtest
     * @return bool
     */
    public static function isWebtest()
    {
        return app()->environment() == "webtest";
    }

    /**
     * online and webtest
     * @return bool
     */
    public static function isProduction()
    {
        return self::isOnline() || self::isWebtest();
    }

    /**
     * qa
     * @return bool
     */
    public static function isTest()
    {
        return app()->environment() == "testing";
    }

    /**
     * dev
     * @return bool
     */
    public static function isDev()
    {
        return app()->environment() == "dev";
    }

    /**
     * development
     * @return bool
     */
    public static function isDevelopment()
    {
        return app()->environment() == "local";
    }
}