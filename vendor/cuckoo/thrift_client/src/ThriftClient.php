<?php

namespace Jiuyan\Cuckoo\ThriftClient;

use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TFramedTransport;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Jiuyan\Cuckoo\ThriftClient\Binary\JiuyanTBinaryProtocol;

class ThriftClient
{
    private $config = array(
        'persist' => false,
        'receive_timeout' => 2000,
        'send_timeout' => 1000,
        'read_buf_size' => 1024,
        'write_buf_size' => 1024,
    );

    private static $supportedTransports = array(
        "TBufferedTransport", "TFramedTransport"
    );

    public function __construct(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function make()
    {
        $config = $this->config;
        $socket = new TSocket($config['host'], $config['real_port'], $config['persist']);
        $socket->setRecvTimeout($config['receive_timeout']);
        $socket->setSendTimeout($config['send_timeout']);

        $transport = self::generateTransport($socket, $config);

        if (!$config['persist']) {
            register_shutdown_function(array($transport, "close"));
        }
        if (isset($config['binary']) && $config['binary'] == 'jiuyan') {
            $protocol = new JiuyanTBinaryProtocol($transport,$config['service_name']);
        } else {
            $protocol = new TBinaryProtocolAccelerated($transport);
        }
        $transport->open();
        $client = new $config['client']($protocol);

        return [$client, $protocol];
    }

    private static function generateTransport($socket, $config)
    {
        $transport = "TFramedTransport";
        if (isset($config["transport"]) && in_array($config["transport"], self::$supportedTransports)) {
            $transport = $config['transport'];
        }
        $className = sprintf('%s\%s', '\Thrift\Transport', $transport);
        return new $className($socket, $config['read_buf_size'], $config['write_buf_size']);
    }

}
