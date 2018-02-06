<?php

include("./SSDB.php");


function test($banyan_cli, $ssdb_cli, $prefix) {
	echo "===test getbit===\n";
    $key = $prefix . "k0";
    $banyan_res = $banyan_cli->getbit($key, array('10'));
    $ssdb_res = $ssdb_cli->getbit($key, array('10'));
    var_dump($banyan_res, $ssdb_res);
	echo "===test getset===\n";
    $key = $prefix . "k0";
    $banyan_res = $banyan_cli->getset($key, array('10'));
    $ssdb_res = $ssdb_cli->getset($key, array('10'));
    var_dump($banyan_res, $ssdb_res);

	echo "===test hsize===\n";
    $key = $prefix . "h0";
    $banyan_res = $banyan_cli->hsize($key);
    $ssdb_res = $ssdb_cli->hsize($key);
    var_dump($banyan_res, $ssdb_res);

	echo "===test multi_hget===\n";
    $key = $prefix . "h1";
    $banyan_res = $banyan_cli->multi_hget($key, array("f0", "f1"));
    $ssdb_res = $ssdb_cli->multi_hget($key, array("f0", "f1"));
    var_dump($banyan_res, $ssdb_res);

	echo "===test multi_hdel===\n";
    $key = $prefix . "h2";
    $banyan_res = $banyan_cli->multi_hdel($key, array("f0", "f1"));
    $ssdb_res = $ssdb_cli->multi_hdel($key, array("f0", "f1"));
    var_dump($banyan_res, $ssdb_res);

	echo "===test zsize===\n";
    $key = $prefix . "z0";
    $banyan_res = $banyan_cli->zsize($key);
    $ssdb_res = $ssdb_cli->zsize($key);
    var_dump($banyan_res, $ssdb_res);

	echo "===test zget===\n";
    $key = $prefix . "z1";
    $banyan_res = $banyan_cli->zget($key, 'xx');
    $ssdb_res = $ssdb_cli->zget($key, 'xx');
    var_dump($banyan_res, $ssdb_res);

	echo "===test qsize===\n";
    $key = $prefix . "q0";
    $banyan_res = $banyan_cli->qsize($key);
    $ssdb_res = $ssdb_cli->qsize($key);
    var_dump($banyan_res, $ssdb_res);

	echo "===test qrange===\n";
    $key = $prefix . "q1";
    $banyan_res = $banyan_cli->qrange($key, "1", "4");
    $ssdb_res = $ssdb_cli->qrange($key, "1", "4");
    var_dump($banyan_res, $ssdb_res);

	echo "===test qclear===\n";
    $key = $prefix . "q2";
    $banyan_res = $banyan_cli->qclear($key);
    $ssdb_res = $ssdb_cli->qclear($key);
    var_dump($banyan_res, $ssdb_res);

	echo "===test qpop===\n";
    $key = $prefix . "q3";
    $banyan_res = $banyan_cli->qpop($key);
    $ssdb_res = $ssdb_cli->qpop($key);
    var_dump($banyan_res, $ssdb_res);
}

$banyan_conf = array ("hosts" => array("10.10.105.5:10024"), "read_timeout_ms" => 3000);
$ssdb_conf = array ("hosts" => array("10.10.105.5:10800"), "read_timeout_ms" => 3000);
#$banyan_cli = SSDBCluster::GetSSDBClient($banyan_conf);
$banyan_cli = new JiuyanSSDB($banyan_conf);
$banyan_cli->change_namespace("test");
$flags = false;
$banyan_cli->change_easy($flags);
#$ssdb_cli = SSDBCluster::GetSSDBClient($ssdb_conf);
$ssdb_cli = new JiuyanSSDB($ssdb_conf);
$ssdb_cli->change_namespace("test");
$ssdb_cli->change_easy($flags);
$t = time();
$prefix = "api_test." . "$t" . "_ssdb_compatible_test_";
test($banyan_cli, $ssdb_cli, $prefix);


?>
