<?php
/**
 * Created by PhpStorm.
 * User: XingHuo
 * Date: 2016/11/28
 * Time: 下午12:34
 */
include '../../../vendor/autoload.php';



use Jiuyan\Qconf\Client\QihooQconf;
use Jiuyan\Qconf\Client\MockQconf;
use Jiuyan\Qconf\Client\JyQconf;


$qconf = new JyQconf();

if ($qconf->isInstallQconf()){
    $qconf->setQconf(new QihooQconf());
}else{
    $mock = new MockQconf();
    $mock->setConfig(__DIR__.'/../Data/test2.json');
    $qconfMock = new JyQconf();
    $qconfMock->setQconf($mock);
}

$data = $qconf->getConf('/message');
var_dump($data);exit;


