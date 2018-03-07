<?php

namespace Jiuyan\Cuckoo\ThriftClient;

use Exception;
use Jiuyan\Cuckoo\ThriftClient\BalanceLoader;
use Jiuyan\Cuckoo\ThriftClient\Client;
use Jiuyan\Cuckoo\ThriftClient\Tracking\TrackedClient;
use Jiuyan\Cuckoo\ThriftClient\ThriftClient;

class ClientFactory
{
    private $config = array(
        'persist' => false,
        'receive_timeout' => 6000,
        'send_timeout' => 1000,
        'read_buf_size' => 1024,
        'write_buf_size' => 1024,
        'authorizations' => array(),
        'tracked' => false,
    );

    // laravel container 用于内部请求
    private $app = null;

    public function __construct($config = array(), $app = null)
    {
        $this->config = array_merge($this->config, $config);
        $this->app = $app;
        $this->balanceLoader = new BalanceLoader();
    }

    public function make($config, $serviceName)
    {
        // 内部调用，直接调用某个类的方法
        if (isset($config['internal']) && $config['internal'] && $this->app) {
            return $this->makeInternalClient($serviceName);
        }

        // RPC调用
        if (isset($config['downgrad_allowed']) && $config['downgrad_allowed']) {
            try {
                list($thriftClient, $protocol) = $this->createThriftClient($config);
                $client = $this->makeClient($thriftClient, $serviceName, $config, $protocol);
            } catch (Exception $e) {
                $client = $this->makeClient(null, $serviceName, $config);
            }
        } else {
            list($thriftClient, $protocol) = $this->createThriftClient($config);
            $client = $this->makeClient($thriftClient, $serviceName, $config, $protocol);
        }

        return $client;
    }

    protected function makeClient($thriftClient, $serviceName, $config, $protocol = null)
    {
        if (isset($config['tracked']) && $config['tracked']) {
            return new TrackedClient($thriftClient, $serviceName, $config, $protocol);
        } else {
            return new Client($thriftClient, $serviceName, $config);
        }
    }

    // 请求内部的某个方法
    public function makeInternalClient($serviceName)
    {
        return $this->createInternalClient($config, $serviceName);
    }

    protected function alias(array $config)
    {
        return isset($config['alias']) ? $config['alias'] : null;
    }

    protected function getNamespace(array $config)
    {
        return isset($config['namespace']) ? $config['namespace'] : null;
    }
    /**
     * 内部调用，直接调用对应的控制器方法
     * @return mixed
     */
    public function createInternalClient($config, $serviceName)
    {
        if ($alias = $this->alias($config)) {
            $alias = rtrim($alias, '\\');
        } else {
            $alias = $this->getServiceName();
        }

        $namespace = $this->getNamespace($config);

        $controller = $namespace . '\\' . $alias;

        if (! class_exists($controller)) {
            throw new ServerException("Service Controller Not Found", 500);
        }

        $client = $this->app->make($controller);

        return new Client($client, $serviceName, $config);
    }

    private function createThriftClient(array $config)
    {
        $thriftClient = new ThriftClient($config);
        return $thriftClient->make();
    }

    private function mergeConfig($config)
    {
        return array_merge($this->config, $config);
    }
}
