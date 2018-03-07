<?php

namespace Jiuyan\Cuckoo\ThriftClient;

use Thrift\Exception\TException;
use Jiuyan\Cuckoo\ThriftClient\ClientFactory;
use Jiuyan\Cuckoo\ThriftClient\BalanceLoader;

class Manager
{
    private $configs = [];
    private $factory = null;
    private static $clients = [];
    protected static $tracer = null;
    protected static $cacher = null;
    protected static $logger = null;
    protected static $stats = null;

    public function __construct(ClientFactory $factory, array $configs)
    {
        $this->factory = $factory;
        $this->configs = $configs;
        $this->balanceLoader = new BalanceLoader();
    }

    public function connect($serviceName)
    {
        if (!isset(static::$clients[$serviceName])) {
            $client = $this->makeRpcClient($serviceName);
            if (!$client) {
                throw new ServerException("All Service Unavailable", 500);
            }
            static::$clients[$serviceName] = $this->decorate($client);
        }

        return static::$clients[$serviceName];
    }

    public function makeRpcClient($serviceName)
    {
        $config = $this->getConfig($serviceName);
        $retry = count($config["hosts"]);
        while ($retry) {
            $host = $this->balanceLoader->chooseHost($config, $this->balanceLoader->getChoosedHostsByServiceName($serviceName));

            $this->balanceLoader->setChoosedHostsWithServiceName($serviceName, $host);
            // 选择host连接
            list($config['host'], $config['real_port']) = $this->separateHostAndPort($host);
            if (empty($config['real_port'])) {
                $config['real_port'] = $config['port'];
            }
            try {
                // 开始连接到server
                $client = $this->factory->make($config, $serviceName);
                $client->setManager($this);
                $client->setHost($config['host']);
                $client->setPort($config['real_port']);
                $hosts = $this->balanceLoader->getChoosedHostsByServiceName($serviceName);
                if (!in_array($host, $hosts)) {
                    array_push($hosts, $host);
                }
                // 记录所有连接请求的host
                $client->setChoosedHosts($hosts);
                return $client;
            } catch (TException $e) {
                if (self::$logger) {
                    $format = "TSocket: Could not connect to %s:%s";
                    self::$logger->error(sprintf($format, $host, $config['port']));
                }
            } finally {
                $retry -= 1;
            }
        }
    }

    public static function setClients($clients)
    {
        static::$clients = $clients;
    }

    public function getConfig($serviceName)
    {
        if (!isset($this->configs[$serviceName])) {
            throw new ServerException($serviceName . " Service Config not found", 500);
        }

        $config = $this->configs[$serviceName];

        if (isset($config['host_picker']) && is_callable($config['host_picker'])) {
            $hosts = $config['host_picker']();
            $config['hosts'] = $hosts;
        }
        return $config;
    }

    public function decorate($client)
    {
        if (self::$logger) {
            $client->setLogger(self::$logger);
        }

        if (self::$cacher) {
            $client->setCacher(self::$cacher);
        }

        if (self::$tracer) {
            $client->setTracer(self::$tracer);
        }

        if(self::$stats) {
            $client->setStats(self::$stats);
        }

        return $client;
    }


    public static function setTracer($tracer)
    {
        static::$tracer = $tracer;
    }

    public static function getTracer()
    {
        return static::$tracer;
    }

    public static function setLogger($logger)
    {
        static::$logger = $logger;
    }

    public static function getLogger()
    {
        return static::$logger;
    }

    public static function setCacher($cacher)
    {
        static::$cacher = $cacher;
    }

    public static function getCacher()
    {
        return static::$cacher;
    }

    public static function setStats($stats)
    {
        static::$stats = $stats;
    }

    public static function getStats()
    {
        return static::$stats;
    }

    public function getFactory()
    {
        return $this->factory;
    }


    protected function separateHostAndPort($ip)
    {
        $res = explode(":", $ip);
        $host = isset($res[0]) ? $res[0] : "";
        $port = isset($res[1]) ? $res[1] : "";
        return [$host, $port];
    }
}
