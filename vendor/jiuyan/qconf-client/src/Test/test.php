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

class Test extends PHPUnit_Framework_TestCase {

    public function testRun(){

        $qconf = new MockQconf();
        $qconf->setConfig(__DIR__.'/../Data/test2.json');

        $qiConf = new QihooQconf();
//


        $key = '/message';
        $data =  $qconf->getConf($key);
        $data1 =  $qiConf->getConf($key);

        $this->assertTrue($data === $data1);
        var_dump($data);
        var_dump($data1);

        $key = '/message/config/app';


        $data =  $qconf->getConf($key);
        $data1 =  $qiConf->getConf($key);

        $this->assertTrue($data === $data1);
        var_dump($data);
        var_dump($data1);



        $key = '/message/config/app';


        $data =  $qconf->getBatchKeys($key, [], 0);
        $data1 =  $qiConf->getBatchKeys($key, [], 0);

        sort($data);
        sort($data1);
        var_dump($data);
        var_dump($data1);
        $this->assertTrue($data === $data1);


        $data =  $qconf->getBatchKeys($key, [], 0);
        $data1 =  $qiConf->getBatchKeys($key, [], 0);
        sort($data);
        sort($data1);
        $this->assertTrue($data === $data1);
        var_dump($data);
        var_dump($data1);


        $data =  $qconf->getBatchConf($key, [], 0);
        $data1 =  $qiConf->getBatchConf($key, [], 0);
        sort($data);
        sort($data1);
        $this->assertTrue($data === $data1);
        var_dump($data);
        var_dump($data1);



        $data =  $qconf->getHost($key, [], 0);
        $data1 =  $qiConf->getHost($key, [], 0);

        $this->assertTrue($data === $data1);
        var_dump($data);
        var_dump($data1);

        $data =  $qconf->getAllHost($key, [], 0);
        $data1 =  $qiConf->getAllHost($key, [], 0);

        $this->assertTrue($data === $data1);
        var_dump($data);
        var_dump($data1);
    }
    public function testFeedback(){
        $qiConf = new QihooQconf();

        echo $qiConf->getConf("/php/in_thrift_service/SLOWLOGTIME");
    }
    public function testJyConf(){
        $jy = new \Jiuyan\Qconf\Client\JyQconf();
        $jy->setQconf(new QihooQconf());
        $d = $jy->getConf("/welfare/SSDB_MULTI_HOST_1",2);
        var_dump($d);
    }
    public function testMockQconf(){
        $qiQconf = new QihooQconf();

        $qconf = new MockQconf();
        $qconf->setMockPath("/Users/XingHuo/IdeaProjects/MongoComposer/spider/",'test','/spider');

        $data = $qconf->getConf("/spider/database/test/enableWrite");
        $data1 = $qiQconf->getConf("/spider/database/test/enableWrite");
        $this->assertTrue($data == $data1);


        $data = $qconf->getConf("/spider/database/test/itugo/slave/dbhost");
        $data1 = $qiQconf->getConf("/spider/database/test/itugo/slave/dbhost");
        $this->assertTrue($data == $data1);



        $data = $qconf->getConf("/spider/memcache/test/user");
        $data1 = $qiQconf->getConf("/spider/memcache/test/user");
        $this->assertTrue(json_decode($data) == json_decode($data1));
        $data1 = $qiQconf->getBatchConf("/spider/database/test/itugo/master",[]);
        var_dump($data1);

        $data = $qconf->getBatchConf("/spider/database/test/itugo/master",[]);
        var_dump($data);
        $this->assertTrue(($data) == ($data1));



        $data1 = $qiQconf->getBatchKeys("/spider/database/test/itugo",[]);
        var_dump($data1);

        $data = $qconf->getBatchKeys("/spider/database/test/itugo",[]);
        var_dump($data);
        $this->assertTrue(($data) == ($data1));

    }
    public function testMockEnv(){
//        $qiQconf = new QihooQconf();
        $qconf = new MockQconf();

        $qconf->setMockEnv('/Users/XingHuo/IdeaProjects/MongoComposer/.env', '/php/in_thrift_service');
        $data = $qconf->getBatchKeys("/php/xxx/pppxx",[]);
        var_dump($data);exit;

    }
}



//
//$value = Qconf::getConf("/xinghuo",'test');
//echo 'get node /demo/confs:  ';print_r($value);
//echo '<br />';
