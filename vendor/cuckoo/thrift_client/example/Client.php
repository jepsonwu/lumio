<?php

use Jiuyan\Cuckoo\ThriftClient\ThriftDao;
use Jiuyan\Cuckoo\ThriftClient\ClientFactory;
use Jiuyan\Cuckoo\ThriftClient\Client;
use Jiuyan\Cuckoo\ThriftClient\Manager;

require dirname(__FILE__) . '/../vendor/autoload.php';
$config = include dirname(__FILE__) . '/../config/config.php';

$factory = new ClientFactory();
$manager = new Manager($factory, $config);

$socket = new \Domnikl\Statsd\Connection\UdpSocket($config['stats']['host'],$config['stats']['port'],$config['stats']['timeout']);
$namespace = $config['stats']['table'].$config['stats']['application'].$config['stats']['department'];
$stats = new Domnikl\Statsd\Client($socket,$namespace);
$manager->setStats($stats);

$dao = new ThriftDao();
$dao->setManager($manager);

$data = $dao->service('local')->call('ping')->run();
echo "response data: \n";
print_r($data);
