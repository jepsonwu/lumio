<?php

namespace Jepsonwu\banyanDB;

use Jepsonwu\banyanDB\structures\HashStructure;

require_once "BanyanDB.php";

/**
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/10/27
 * Time: 下午1:38
 */
class BanyanTestFactory
{
    private static $banyanConfig = [
        'hosts' => [
            "10.10.106.28:10500",
        ],
        'max_reconnect_tries' => 2
    ];

    const BANYAN_NAMESPACE = "in_circle_chat";

    const BANYAN_TABLE = 'common';

    const NAME_DEMO_HASH = "demo_hash";
    const NAME_DEMO_SET = "demo_set";
    const NAME_DEMO_KEY = "demo_key";
    const NAME_DEMO_SPECIAL_KEY = "demo_special_key";
    const NAME_DEMO_QUEUE = "demo_queue";

    /**
     * @var \BanyanClient
     */
    private static $instance;

    /**
     * @param $namespace
     * @param $prefix
     * @return \BanyanClient
     */
    public static function getInstance($namespace, $prefix)
    {
        $key = $namespace . $prefix;
        !isset(self::$instance[$key]) &&
        self::$instance[$key] = \BanyanDBCluster::GetBanyanClient(self::$banyanConfig, $namespace, $prefix);

        return self::$instance[$key];
    }

    /**
     * @param null $name
     * @param int $type
     * @return InterfaceBanyan|SpecialInterfaceBanyan
     */
    public static function common($name = null, $type = BanyanFactory::KEY_STRUCTURE)
    {
        BanyanFactory::registerGenerateBanyanCallback("Jepsonwu\\banyanDB\\BanyanTestFactory::getInstance");
        return BanyanFactory::getInstance(self::BANYAN_NAMESPACE, self::BANYAN_TABLE, $name, $type);
    }

    /**
     * @return InterfaceBanyan|HashStructure
     */
    public static function demoHash()
    {
        return self::common(self::NAME_DEMO_HASH, BanyanFactory::HASH_STRUCTURE);
    }

    /**
     * @return InterfaceBanyan
     */
    public static function demoSet()
    {
        return self::common(self::NAME_DEMO_SET, BanyanFactory::SET_STRUCTURE);
    }

    /**
     * @return SpecialInterfaceBanyan
     */
    public static function demoSpecialKey()
    {
        return self::common(self::NAME_DEMO_SPECIAL_KEY, BanyanFactory::SPECIAL_KEY_STRUCTURE);
    }

    /**
     * @return SpecialInterfaceBanyan
     */
    public static function demoQueue()
    {
        return self::common(self::NAME_DEMO_QUEUE, BanyanFactory::QUEUE_STRUCTURE);
    }
}
