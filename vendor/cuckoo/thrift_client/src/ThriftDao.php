<?php

namespace Jiuyan\Cuckoo\ThriftClient;

class ThriftDao
{
    private static $manager = null;

    private $serviceName = null;

    public function service($serviceName)
    {
        $client = $this->getClient($serviceName);
        return $this->createAttacher($client);
    }

    public function setManager($manager)
    {
        static::$manager = $manager;
    }

    public function getServiceName($serviceName = null)
    {
        return $serviceName ?: $this->serviceName;
    }

    public function getClient($serviceName)
    {
        return static::$manager->connect($this->getServiceName($serviceName));
    }

    public function createAttacher($client)
    {
        return new Attacher($client);
    }
}

