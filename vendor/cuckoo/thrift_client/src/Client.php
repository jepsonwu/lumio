<?php

namespace Jiuyan\Cuckoo\ThriftClient;

use Exception;
use Thrift\Type\TMessageType;
use Jiuyan\Cuckoo\ThriftClient\Tracking\HeaderArgs;
use Jiuyan\Cuckoo\ThriftClient\Tracking\RequestHeader;
use Jiuyan\Cuckoo\ThriftClient\Manager;

class Client
{
    private $thriftClient = null;
    private $config = null;
    private $serviceName = null;
    private $logger = null;
    private $cacher = null;
    protected $tracer = null;
    protected $stats = null;
    // 重试的时候用于构建新的client
    protected $manager = null;
    // 记录已经尝试过的host
    private $choosedHosts = [];
    // 目前正在使用的host
    private $host = null;
    private $port = null;

    public function __construct($thriftClient, $serviceName, $config)
    {
        $this->thriftClient = $thriftClient;
        $this->serviceName = $serviceName;
        $this->config = $config;
    }

    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function makeNewClient($config, $serviceName)
    {
        return $this->manager->getFactory()->make($config, $serviceName);
    }

    public function isDowngrade()
    {
        return (bool)!$this->thriftClient;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    public function getServiceName()
    {
        return $this->serviceName;
    }

    public function setChoosedHosts($host)
    {
        $hosts = is_array($host) ? $host : [$host];
        $this->choosedHosts = array_merge($this->choosedHosts, $hosts);
    }

    public function getChoosedHosts()
    {
        return $this->choosedHosts;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setCacher($cacher)
    {
        $this->cacher = $cacher;
    }

    public function getCacher()
    {
        return $this->cacher;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setTracer($tracer)
    {
        $this->tracer = $tracer;
    }

    public function getTracer()
    {
        return $this->tracer;
    }

    public function setStats($stats)
    {
        $this->stats = $stats;
    }

    public function getStats()
    {
        return $this->stats;
    }

    public function request($method, array $args = [])
    {
        $connectionInfo = json_encode($this->getConnectionInfo());
        $serviceName = $this->getServiceName();
        $prefix = $serviceName.".".$method.".";
        if (isset($this->stats)) {
            $this->stats->startTiming($prefix . $connectionInfo);
        }
        $res = call_user_func_array(array($this->thriftClient, $method), $args);
        $this->errorStats($res,$this->stats,$prefix,$connectionInfo);
        if (isset($this->stats)) {
            $this->stats->endTiming($prefix . $connectionInfo);
        }
        return $res;
    }

    public function getConnectionInfo()
    {
        return array(
            "service_name" => $this->getServiceName(),
            "host" => $this->getHost(),
            "port" => $this->getPort()
        );
    }

    public function errorStats($result, $stats, $prefix, $connectionInfo)
    {
        if (isset($stats) && isset($result) && is_string($result)) {
            $res = json_decode($result, true);
            if (is_array($res) && !$res['succ']) {
                $error = isset($res['code']) ? $res['code'] : 0;
                $stats->count($prefix . ".error." . $error . $connectionInfo);
            }
        }
    }
}
