<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/4
 * Time: 下午4:31
 */

namespace Modules\User\Components\Extensions;

class ExtensionFactory
{
    private static $instance;

    protected static function getInstance($className, $userId)
    {

        $className = "Modules\\User\\Components\\Extensions\\{$className}Extension";
        $key = md5($className . $userId);
        isset(self::$instance[$key]) || self::$instance[$key] = new $className($userId);

        return self::$instance[$key];
    }

    /**
     * @param $userId
     * @return AppExtension
     */
    public static function App($userId)
    {
        return self::getInstance("App", $userId);
    }

    /**
     * @param $userId
     * @return LoginExtension
     */
    public static function Login($userId)
    {
        return self::getInstance("Login", $userId);
    }
}