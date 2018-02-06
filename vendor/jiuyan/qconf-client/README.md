为了降低本地部署qconf的成本 所以开发了这个客户端 

bin 下面的 QMake 生成 qconf配置中心的镜像文件 为了本地mock 提供数据支持

Data 目录自带示例 跑单测用

```php
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
//确定当前环境是否支持
if ($qconf->isInstallQconf()){  
    $qconf->setQconf(new QihooQconf());
}else{
    $mock = new MockQconf();
    //设置mock的数据
    $mock->setConfig(__DIR__.'/../Data/test2.json'); 
    $qconfMock = new JyQconf();
    $qconfMock->setQconf($mock);
}

$data = $qconf->getConf('/message');
var_dump($data);exit;

```