<?php

namespace Jiuyan\Cuckoo\ThriftClient\Tracking;

use Exception;
use Jiuyan\Cuckoo\ThriftClient\Client;
use Thrift\Type\TMessageType;

class TrackedClient extends Client
{
    protected $protocol = null;
    protected $globalTags = [];

    protected $context = array(
        "trace_id" => '',  // 全局的trace_id
        "parent_span_id" => '',  // 分类的span_id
        "sampled" => false, // 是否需要上传
        "seq_id" => '',
        "flags" => 0,
        "meta" => [],
    );

    # 传递的HEADER的method
    const TRACED_METHOD = "__jiuyan_service__header__v1__";

    public function __construct($client, $serviceName, $config, $protocol = null)
    {
        parent::__construct($client, $serviceName, $config, $protocol);
        $this->protocol = $protocol;
    }

    public function request($method, array $args = Array())
    {
        if (!$this->tracer) {
            return parent::request($method, $args);
        }

        $context = $this->createContext();
        $startTime = $this->tracer->generatZipkinTimestamp();
        $exception = null;
        $failed = 0;
        try {
            $config = $this->getConfig();
            if($config['trace_header']) {
                // 发送头信息
                $this->sendHeader($context);
            }
            $res = parent::request($method, $args);
            $failed = $this->assertFailed($res);

        } catch (Exception $e) {
            $failed = 1;
            $exception = $e;
        }
        try {
            if ($context['sampled']) {
                $connection = $this->getConnectionInfo();
                $method = $this->createMethod($method, $args);
                if ($failed == 1) {
                    $this->setGlobalTags(array('error' => $failed));
                }
                $this->createParamTags($args);
                $tags = $this->getGlobalTags();
                $this->tracer->startSpan($connection, $method, $startTime, $tags, $context['trace_id'], $context['parent_span_id']);
            }
        } catch (Exception $e) {
            $logger = $this->getLogger();
            if($logger){
                $logger->error($e->getMessage()."\n".$e->getTraceAsString());
            }
        }

        if ($exception !== null) {
            throw $exception;
        }

        return $res;
    }

    public function assertFailed($res)
    {
        try {
            $res = json_decode($res, true);
            if (isset($res['code'])) {
                $statusCode = substr($res['code'], 0, 3);
                if ($statusCode == 500 || $statusCode == 400) {
                    return 1;
                }
            }
            return 0;
        } catch (Exception $e) {
            return 1;
        }
    }

    public function createMethod($method, array $args)
    {
        // inthrift 特殊处理
        if ($method != 'call') {
            return $method;
        }
        try {
            return $args[0];
        } catch (Exception $e) {
            return $method;
        }
    }

    public function getGlobalTags()
    {
        return $this->globalTags;
    }

    public function setGlobalTags(array $tags)
    {
        $this->globalTags = array_merge($this->globalTags, $tags);
    }

    public function createParamTags(array $params)
    {
        $tags = [];
        foreach ($params as $key => $parma) {
            // 有可能thrift的请求参数是object
            if (is_object($parma)) {
                continue;
            }
            $tags[$key] = $parma;
        }
        $data = array('params' => json_encode($tags));
        return $this->setGlobalTags($data);
    }

    public function createContext($context = [])
    {
        $context = $this->tracer->getRootSpanContext();
        $context['parent_span_id'] = $context['span_id'];
        return array_merge($this->context, $context);
    }

    protected function sendHeader(array $context)
    {
        $HeaderArgs = new HeaderArgs();
        $header = new RequestHeader($context);
        $HeaderArgs->header = $header;
        $this->protocol->writeMessageBegin(self::TRACED_METHOD, TMessageType::CALL, 0);
        $HeaderArgs->write($this->protocol);
        $this->protocol->writeMessageEnd();
    }
}
