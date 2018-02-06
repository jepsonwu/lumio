<?php

namespace BanyanDB;

use Exception;


class BanyanDBCluster
{
    private static $_banyandb_client;

    public static function GetBanyanClient($conf = array(), $ns, $tab)
    {

        $key = md5(serialize($conf) . $ns . $tab);
        if (!isset(self::$_banyandb_client[$key])) {
            $hosts = array();
            if (isset($conf['hosts'])) {
                foreach ($conf['hosts'] as $h) {
                    array_push($hosts, $h);
                }
            }
            #var_dump($hosts);
            if (count($hosts) < 1) {
                throw new BanyanDBException("no hosts");
            }

            $timeout_ms = 3000;
            if (isset($conf['read_timeout_ms'])) {
                $timeout_ms = $conf['read_timeout_ms'];
            }

            $retries = 3;
            if (isset($conf['max_request_retry'])) {
                $retries = $conf['max_request_retry'];
            }
            #var_dump($timeout_ms);
            #var_dump($retries);
            BanyanDBCluster::$_banyandb_client[$key] = new BanyanClient($hosts, $ns, $tab, $timeout_ms, $retries);
        }

        return BanyanDBCluster::$_banyandb_client[$key];
    }

    public static function DestroyBanyanClient()
    {
        if (BanyanDBCluster::$_banyandb_client instanceof BanyanClient) {
            BanyanDBCluster::$_banyandb_client->close();
        }

        BanyanDBCluster::$_banyandb_client = NULL;
    }
}
