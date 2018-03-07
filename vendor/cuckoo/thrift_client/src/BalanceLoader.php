<?php

namespace Jiuyan\Cuckoo\ThriftClient;

class BalanceLoader
{
    public $choosedHosts = [];

    public function setChoosedHostsWithServiceName($serviceName, $host)
    {
        $hosts = is_array($host) ? $host : [$host];

        if (! isset($this->choosedHosts[$serviceName])) {
            $this->choosedHosts[$serviceName] = [];
        }

        $choosedHosts = array_merge($this->choosedHosts[$serviceName], $hosts);

        $this->choosedHosts[$serviceName] = array_unique($choosedHosts);
    }

    public function getChoosedHostsByServiceName($serviceName)
    {
        if (isset($this->choosedHosts[$serviceName])) {
            return $this->choosedHosts[$serviceName];
        } else {
            return [];
        }
    }

    public function chooseHost($config, $choosedHosts = [])
    {
        $hosts = array_diff($config['hosts'], $choosedHosts);
        if ($hosts) {
            $host = $hosts[array_rand($hosts)];
        } else {
            $host = $config['hosts'][0];
        }
        return $host;
    }
}
