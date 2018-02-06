<?php
namespace Jiuyan\CommonCache;

use Jiuyan\CommonCache\Handles\BanyanDBHandle;
use Jiuyan\CommonCache\Iterators\RScanIterator;
use Jiuyan\CommonCache\Iterators\ScanIterator;
use Jiuyan\CommonCache\Structures\HashStructure;
use Jiuyan\CommonCache\Structures\KeyStructure;
use Jiuyan\CommonCache\Structures\SetStructure;
use Jiuyan\CommonCache\Structures\SpecialKeyStructure;

/**
 * banyan factory
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/27
 * Time: 10:27
 */
class BanyanFactory
{
    const KEY_STRUCTURE = 1;//key value, name is null
    const SPECIAL_KEY_STRUCTURE = 2;//special key value, name is key
    const HASH_STRUCTURE = 3;//hash
    const SET_STRUCTURE = 4;//set

    private static $instance;

    /**
     * @param $namespace
     * @param $table
     * @param $name
     * @param $type
     * @param $generateBanyanCallback |you must register callback that use for generate banyan instance
     * @return mixed|InterfaceBanyan
     */
    public static function getInstance($namespace, $table, $name, $type, $generateBanyanCallback = null)
    {
        $key = md5($namespace . $table . $name . $type);
        if (!isset(self::$instance[$key])) {
            switch ($type) {
                case self::HASH_STRUCTURE:
                    $model = new HashStructure($namespace, $table, $name);
                    break;
                case self::SET_STRUCTURE:
                    $model = new SetStructure($namespace, $table, $name);
                    break;
                case self::SPECIAL_KEY_STRUCTURE:
                    $model = new SpecialKeyStructure($namespace, $table, $name);
                    break;
                default:
                    $model = new KeyStructure($namespace, $table, null);
                    break;
            }
            $model->setGenerateBanyanCallback($generateBanyanCallback);
            self::$instance[$key] = $model;
        }

        return self::$instance[$key];
    }

    public static function getScanIterator(InterfaceBanyan $interfaceBanyan)
    {
        return new ScanIterator($interfaceBanyan);
    }

    public static function getRScanIterator(InterfaceBanyan $interfaceBanyan)
    {
        return new RScanIterator($interfaceBanyan);
    }
}