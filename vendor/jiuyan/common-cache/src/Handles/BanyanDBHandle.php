<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/13
 * Time: 10:18
 */

namespace Jiuyan\CommonCache\Handles;

use BanyanDB\BanyanClient;
use BanyanDB\BanyanDBCluster;

class BanyanDBHandle
{
    /**
     * @var BanyanClient
     */
    public static $instance;
    /**
     * @param $namespace
     * @param $prefix
     * @return BanyanClient
     */
    public static function getHandle($namespace, $prefix)
    {
        $key = $namespace . $prefix;
        if (!isset(self::$instance[$key])) {
            self::$instance[$key] = BanyanDBCluster::GetBanyanClient(config('common_cache.servers.banyandb'), $namespace, $prefix);
        }
        return self::$instance[$key];
    }
}
