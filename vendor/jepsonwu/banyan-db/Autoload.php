<?php

/**
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/10/27
 * Time: 下午2:56
 */
class Autoload
{
    public static function loadByNamespace($name)
    {
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $name);

        $classFile = substr($classPath, strlen('Jepsonwu/banyanDB')) . '.php';
        $testsClassFile = __DIR__ . DIRECTORY_SEPARATOR . "tests" . $classFile;
        $srcClassFile = __DIR__ . DIRECTORY_SEPARATOR . "src" . $classFile;

        if (is_file($testsClassFile)) {
            require_once($testsClassFile);
        }

        if (is_file($srcClassFile)) {
            require_once($srcClassFile);
        }

        if (class_exists($name, false)) {
            return true;
        }

        return false;
    }
}

spl_autoload_register('Autoload::loadByNamespace');

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../vendor/autoload.php";
