<?php

namespace Jiuyan\Cuckoo\ThriftClient;

use Closure;
use Exception;
use Jiuyan\Cuckoo\ThriftClient\Client;
use Thrift\Exception\TException;
use Thrift\Exception\TApplicationException;
use Jiuyan\Cuckoo\ThriftClient\BalanceLoader;

class Attacher
{
    private $client = null;
    private $balanceLoader = null;
    private $method = null;
    private $args = [];
    private $cachedTime = null;
    private $default = null;
    private $times = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->balanceLoader = new BalanceLoader();
    }

    public function call($method)
    {
        $this->method = $method;
        return $this;
    }

    public function with()
    {
        $this->args = func_get_args() ?: array();
        return $this;
    }

    public function cached($cachedTime)
    {
        $this->cachedTime = $cachedTime;
        return $this;
    }

    public function downgrade($default)
    {
        $this->default = $default;
        return $this;
    }

    public function retry($times)
    {
        $this->times = $times;
        return $this;
    }

    public function doRetry()
    {
        $times = $this->times;
        $client = $this->client;
        while ($times) {
            try {
                $startTime = microtime(true);
                $config = $this->client->getConfig();
                $host = $this->balanceLoader->chooseHost($config, $client->getChoosedHosts());
                $config['host'] = $host;
                $newClient = $this->client->makeNewClient($config, $client->getServiceName());
                $newClient->setHost($host);
                $result = $newClient->request($this->method, $this->args);
                $this->logRequest($startTime);
                return $result;
            } catch (TException $e) {
                // 下次排除这个host
                $client->setChoosedHosts($host);
                $this->logException($e, $startTime);
                $times -= 1;
                if ($times < 1) {
                    $this->handerException($e);
                }
            } finally {
                $this->statsd();
            }
        }
    }

    public function run()
    {
        if ($this->client->isDowngrade()) {
            UserException::serviceDowngrade();
        }
        $startTime = microtime(true);
        try {
            $result = $this->client->request($this->method, $this->args);
            $this->logRequest($startTime);
        } catch (TException $e) {

            $this->logException($e, $startTime);
            if ($this->default) {
                return $this->default;
            }
            if ($this->times) {
                return $this->doRetry();
            }
            $this->handerException($e);
        } finally {

        }
        return $result;
    }

    public function result($default = null)
    {
        try {
            return $this->run();
        } catch (Exception $e) {
            return $default;
        }
    }

    public function statsd()
    {
        # TODO RPC请求数, 响应时间，错误数统计, 用于自动接口降级, 剔除故障机器
        return true;
    }

    public function handerException($e)
    {
        $className = get_class($e);
        if ($this->isUserException($e)) {
            throw new UserException($e->getMessage(), $e->error_code, $e->error_name);
        } elseif ($this->isSystemException($e)) {
            throw new ServerException($e->getMessage(), $e->error_code);
        } else {
            throw new ServerException($e->getMessage(), TApplicationException::UNKNOWN);
        }
    }

    public function data($default = null)
    {
        try {
            return $this->run();
        } catch(UserException $e) {
            return $default;
        }
    }

    public function cachedData($default = null)
    {
        if ($this->getCacheMinutes() && $this->client->getCacher()) {
            return $this->getCached();
        }
        return $this->run();
    }

    public function getCacheMinutes()
    {
        return $this->cachedTime;
    }

    public function logException($exception, $startTime)
    {
        if (! $logger = $this->client->getLogger()) {
            return;
        }
        $message = $this->getRequestMessage($startTime) . '->' . $exception->getMessage();
        if ($this->isUserException($exception)) {
            $logger->warning($message, $this->args);
        } else {
            $logger->error($message, $this->args);
        }
    }

    public function logRequest($startTime)
    {
        if (!$logger = $this->client->getLogger()) {
            return;
        }
        $message = $this->getRequestMessage($startTime);
        $logger->info($message, $this->args);
    }

    public function getRequestMessage($startTime)
    {
        $durtion = intval((microtime(true) - $startTime) * 1000);
        $format = "%s:%s [%f]ms";
        $host = $this->client->getHost();
        return sprintf($format, $host, $this->getMethodFullname(), $durtion);
    }

    public function getCacheKey()
    {
        return md5(sprintf('%s.%s', $this->getMethodFullname(), json_encode($this->args)));
    }

    private function getMethodFullname($glue = '@')
    {
        return $this->client->getServiceName() . $glue . $this->method;
    }

    public function getCacheCallback()
    {
        return function() {
            return $this->run();
        };
    }

    public function getCached()
    {
        $key = $this->getCacheKey();
        $minutes = $this->getCacheMinutes();
        $callback = $this->getCacheCallback();
        $cacher = $this->client->getCacher();
        return $this->remember($cacher, $key, $minutes, $callback);
    }

    private function isUserException($exception)
    {
        $exceptionClass = get_class($exception);
        $retval = strpos($exceptionClass, 'User');
        return $retval != false;
    }

    private function isSystemException($exception)
    {
        $exceptionClass = get_class($exception);
        $retval = strpos($exceptionClass, 'System');
        return $retval != false;
    }

    public function remember($cacher, $key, $minutes, Closure $callback)
    {
        if (($value = $cacher->get($key))) {
            return $value;
        }
        $cacher->add($key, $value = $callback(), $minutes);
        return $value;
    }
}
