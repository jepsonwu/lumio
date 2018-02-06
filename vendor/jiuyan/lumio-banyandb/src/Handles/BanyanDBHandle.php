<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/11/29
 * Time: 下午5:50
 */

namespace Jiuyan\Lumio\BanyanDB\Handles;

use BanyanDB\BanyanClient;
use BanyanDB\BanyanDBCluster;

class BanyanDBHandle
{
    /**
     * @var BanyanClient
     */
    public static $clientInstance;

    /**
     * @param $namespace
     * @param $table
     * @return BanyanClient
     */
    public static function getClient($namespace, $table)
    {
        $key = $namespace . $table;
        if (!isset(self::$clientInstance[$key])) {
            self::$clientInstance[$key] = BanyanDBCluster::GetBanyanClient(
                config('banyandb.servers'),
                $namespace,
                $table
            );
        }

        return self::$clientInstance[$key];
    }
}
