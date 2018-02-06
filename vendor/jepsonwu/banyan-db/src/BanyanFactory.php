<?php

namespace Jepsonwu\banyanDB;

use Jepsonwu\banyanDB\structures\HashStructure;
use Jepsonwu\banyanDB\structures\QueueStructure;
use Jepsonwu\banyanDB\structures\SetStructure;
use Jepsonwu\banyanDB\structures\SpecialKeyStructure;
use Jepsonwu\banyanDB\structures\KeyStructure;
use Jepsonwu\banyanDB\iterators\RScanIterator;
use Jepsonwu\banyanDB\iterators\ScanIterator;

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
    const QUEUE_STRUCTURE = 5;//queue

    private static $instance;

    private static $generateBanyanCallback;

    /**
     * @param $namespace
     * @param $table
     * @param $name
     * @param $type
     * @return InterfaceBanyan|SpecialInterfaceBanyan
     */
    public static function getInstance($namespace, $table, $name, $type)
    {
        $key = md5($namespace . $table . $name . $type);
        if (!isset(self::$instance[$key])) {
            switch ($type) {
                case self::HASH_STRUCTURE:
                    $structure = new HashStructure($namespace, $table, $name);
                    break;
                case self::SET_STRUCTURE:
                    $structure = new SetStructure($namespace, $table, $name);
                    break;
                case self::SPECIAL_KEY_STRUCTURE:
                    $structure = new SpecialKeyStructure($namespace, $table, $name);
                    break;
                case self::QUEUE_STRUCTURE:
                    $structure = new QueueStructure($namespace, $table, $name);
                    break;
                default:
                    $structure = new KeyStructure($namespace, $table, null);
                    break;
            }

            $structure->setGenerateBanyanCallback(self::$generateBanyanCallback);
            self::$instance[$key] = $structure;
        }

        return self::$instance[$key];
    }

    /**
     * you must register callback that use for generate banyan instance
     * @param $generateBanyanCallback
     * @return string
     */
    public static function registerGenerateBanyanCallback($generateBanyanCallback)
    {
        is_null(self::$generateBanyanCallback) &&
        self::$generateBanyanCallback = $generateBanyanCallback;

        return self::class;
    }

    /**
     * @param InterfaceBanyan $interfaceBanyan
     * @return ScanIterator
     */
    public static function getScanIterator(InterfaceBanyan $interfaceBanyan)
    {
        return new ScanIterator($interfaceBanyan);
    }

    /**
     * @param InterfaceBanyan $interfaceBanyan
     * @return RScanIterator
     */
    public static function getRScanIterator(InterfaceBanyan $interfaceBanyan)
    {
        return new RScanIterator($interfaceBanyan);
    }
}
