<?php

include("./SSDB.php");


function test_kv($cli, $prefix) {
	echo "===test kv===\n";
	# get set del incr decr exists getbit setbit
	$key = $prefix . "k0";
	$val = "v0";
	$res = $cli->set($key, $val);
    var_dump($res);
	var_dump($res == 1 ? True : False);
	$res = $cli->get($key);
	var_dump($res == $val ? True : False);
	$res = $cli->del($key);
	var_dump($res == 1 ? True : False);
	$res = $cli->get($key);
	var_dump($res == NULL ? True : False);
	$res = $cli->exists($key);
	var_dump($res == False ? True : False);
	$val = "888";
	$res = $cli->incr($key, $val);
	var_dump($res == 888 ? True : False);
	$res = $cli->decr($key, "444");
	var_dump($res == 444 ? True : False);
	$res = $cli->exists($key);
	var_dump($res == True ? True : False);
	$key = $prefix . "k1";
	$res = $cli->setbit($key, "123456", "1");
	var_dump($res == 0 ? True : False);
	$res = $cli->getbit($key, "123456");
	var_dump($res == 1 ? True : False);
	# 10 val_dump

	# multi_set multi_get multi_set
	echo "===test multi_*===\n";
	$key1 = $prefix . "k11";
	$key2 = $prefix . "k12";
	$key3 = $prefix . "k13";
	$res = $cli->multi_set(array("$key1" => "v11", "$key2" => "v12", "$key3" => "v13"));
	var_dump($res == 3 ? True : False);
	$res = $cli->multi_get(array("$key1", "$key2", "$key3"));
    var_dump($res);
	#var_dump($res[$key2] == "v12" ? True : False);
    #$ret_key2 = $prefix_ret . "k12";
	#var_dump($res[$ret_key2] == "v12" ? True : False);
	$res = $cli->multi_del(array("$key1", "$key2", "$key3"));
	var_dump($res == 3 ? True : False);

	$key1 = $prefix . "hk0";
	$key2 = $prefix . "hk1";
	$key3 = $prefix . "hk2";
	$res = $cli->multi_hset($key1, array("f0" => "v0", "f1" => "v1", "f2" => "v3"));
	var_dump($res == 3 ? True : False);
	$res = $cli->multi_hset($key2, array("f0" => "v0", "f1" => "v1", "f2" => "v3"));
	var_dump($res == 3 ? True : False);
	$res = $cli->multi_hset($key3, array("f0" => "v0", "f1" => "v1", "f2" => "v3"));
    var_dump($res);
	var_dump($res == 3 ? True : False);
	$res = $cli->mhmget(array($key1, $key2, $key3), array("f0", "f1", "f2"));
    $val1 = array("f0" => "v0", "f1" => "v1", "f2" => "v3");
	#var_dump($res);
    var_dump($res[$key1] == $val1 ? True : False);

    $key = $prefix . "hklonglongtest";
    $kvs = array();
    for ($i = 0; $i < 1000; ++$i) {
        $filed = "faaaaaaaaaaaaaaaaaaaaaaaabbbbbbbbbbbbbbbbbbbbbbbbbbccccccccccccccccccccccdddddddddddddddddd$i";
        #$filed = "fa$i";
        $val = "xx";
        array_push($kvs, array($filed => $val));
    }   
    $res = $cli->multi_hset($key, $kvs);
    #var_dump("multi_hset", $res);
	var_dump($res == 1000 ? True : False);

	echo "===test list===\n";
	for ($i = 100; $i < 1000; ++$i) {
		$key = $prefix . sprintf("k%08d", $i);
		$val = "$i";
		$cli->set($key, $val);
	}
	$start = $prefix . sprintf("k%08d", 800); 
	$end = $prefix . sprintf("k%08d", 808);
	$res = $cli->scan($start, $end, "10");
	#var_dump($res[$start]);
	#var_dump(count($res) == 8 && $res[$end] == "808" ? True : False);
	var_dump(count($res) == 8 ? True : False);
	$res = $cli->rscan($end, $start, "10");
	#var_dump(count($res) == 8 && $res[$start] == "800" ? True : False);
	var_dump(count($res) == 8 ? True : False);
	$res = $cli->keys($start, $end, "10");
	var_dump(count($res) == 8 ? True : False);
	$res = $cli->rkeys($end, $start, "10");
	var_dump(count($res) == 8 ? True : False);

	for ($i = 0; $i < 100; ++$i) {
		$key = $prefix . sprintf("h%08d", $i);
		$res = $cli->hset($key, "f", "$i");
	}
	$start = $prefix . sprintf("h%08d", 80); 
	$end = $prefix . sprintf("h%08d", 88);
	$res = $cli->hlist($start, $end, "10");
	var_dump(count($res) == 8 && $res[7] == $end ? True : False);
	$res = $cli->hrlist($end, $start, "10");
	#var_dump($res);
	var_dump(count($res) == 8 && $res[7] == $start ? True : False);

	for ($i = 0; $i < 100; ++$i) {
		$key = $prefix . sprintf("z%08d", $i);
		$member = "f$i";
		$res = $cli->zset($key, $member, "$i");
	}
	$start = $prefix . sprintf("z%08d", 80); 
	$end = $prefix . sprintf("z%08d", 88);
	$res = $cli->zlist($start, $end, "10");
	var_dump(count($res) == 8 && $res[7] == $end ? True : False);
	$res = $cli->zrlist($end, $start, "10");
	#var_dump($res);
	var_dump(count($res) == 8 && $res[7] == $start ? True : False);
    $key = $prefix . "jk100";
    $res = $cli->jdelay("set", $key,  "2", 1);
    var_dump($res);
}

function test_router($cli, $prefix) {
	echo "===test kv===\n";
	# get set del incr decr exists getbit setbit
	$key = $prefix . "k0";
	$val = "v0";
	$res = $cli->set($key, $val);
	var_dump($res == 1 ? True : False);
	$res = $cli->get($key);
	var_dump($res == $val ? True : False);
	$res = $cli->del($key);
	var_dump($res == 1 ? True : False);
	$res = $cli->get($key);
	var_dump($res == NULL ? True : False);
	$res = $cli->exists($key);
	var_dump($res == False ? True : False);
	$val = "888";
	$res = $cli->incr($key, $val);
	var_dump($res == 888 ? True : False);
	$res = $cli->decr($key, "444");
	var_dump($res == 444 ? True : False);
	$res = $cli->exists($key);
	var_dump($res == True ? True : False);
	$key = $prefix . "k1";
	$res = $cli->setbit($key, "123456", "1");
	var_dump($res == 0 ? True : False);
	$res = $cli->getbit($key, "123456");
	var_dump($res == 1 ? True : False);
	# 10 val_dump

	# multi_set multi_get multi_set
	echo "===test multi_*===\n";
	$key1 = $prefix . "k11";
	$key2 = $prefix . "k12";
	$key3 = $prefix . "k13";
	$res = $cli->multi_set(array("$key1" => "v11", "$key2" => "v12", "$key3" => "v13"));
	#var_dump($res);
	var_dump($res == 3 ? True : False);
	$res = $cli->multi_get(array("$key1", "$key2", "$key3"));
	var_dump($res);
	#var_dump($res[$key2] == "v12" ? True : False);
	$res = $cli->multi_del(array("$key1", "$key2", "$key3"));
	var_dump($res == 3 ? True : False);

	$key1 = $prefix . "hk0";
	$key2 = $prefix . "hk1";
	$key3 = $prefix . "hk2";
	$res = $cli->multi_hset($key1, array("f0" => "v0", "f1" => "v1", "f2" => "v3"));
	var_dump($res == 3 ? True : False);
	$res = $cli->multi_hset($key2, array("f0" => "v0", "f1" => "v1", "f2" => "v3"));
	var_dump($res == 3 ? True : False);
	$res = $cli->multi_hset($key3, array("f0" => "v0", "f1" => "v1", "f2" => "v3"));
	var_dump($res == 3 ? True : False);
	$res = $cli->mhmget(array($key1, $key2, $key3), array("f0", "f1", "f2"));
    $val1 = array("f0" => "v0", "f1" => "v1", "f2" => "v3");
    var_dump($res[$key1] == $val1 ? True : False);
	var_dump($res);

    $key = $prefix . "hklonglongtest";
    $kvs = array();
    for ($i = 0; $i < 1000; ++$i) {
        $filed = "faaaaaaaaaaaaaaaaaaaaaaaabbbbbbbbbbbbbbbbbbbbbbbbbbccccccccccccccccccccccdddddddddddddddddd$i";
        $val = "";
        array_push($kvs, array($filed => $val));
    }   
    $res = $cli->multi_hset($key, $kvs);
	var_dump($res == 1000 ? True : False);

	echo "===test list===\n";
	for ($i = 100; $i < 1000; ++$i) {
		$key = $prefix . sprintf("k%08d", $i);
		$val = "$i";
		$cli->set($key, $val);
	}
	$start = $prefix . sprintf("k%08d", 800); 
	$end = $prefix . sprintf("k%08d", 808);
	$res = $cli->scan($start, $end, "10");
	var_dump($res);
	#var_dump(count($res) == 8 && $res[$end] == "808" ? True : False);
	$res = $cli->rscan($end, $start, "10");
	var_dump($res);
	#var_dump(count($res) == 8 && $res[$start] == "800" ? True : False);
	$res = $cli->keys($start, $end, "10");
	var_dump(count($res) == 8 ? True : False);
	$res = $cli->rkeys($end, $start, "10");
	var_dump(count($res) == 8 ? True : False);

	for ($i = 0; $i < 100; ++$i) {
		$key = $prefix . sprintf("h%08d", $i);
		$res = $cli->hset($key, "f", "$i");
	}
	$start = $prefix . sprintf("h%08d", 80); 
	$end = $prefix . sprintf("h%08d", 88);
	$res = $cli->hlist($start, $end, "10");
	$res = $cli->hrlist($end, $start, "10");
	var_dump($res);
	#var_dump(count($res) == 8 && $res[7] == $end ? True : False);
	$res = $cli->hrlist($end, $start, "10");
	var_dump($res);
	#var_dump(count($res) == 8 && $res[7] == $start ? True : False);

	for ($i = 0; $i < 100; ++$i) {
		$key = $prefix . sprintf("z%08d", $i);
		$member = "f$i";
		$res = $cli->zset($key, $member, "$i");
	}
	$start = $prefix . sprintf("z%08d", 80); 
	$end = $prefix . sprintf("z%08d", 88);
	$res = $cli->zlist($start, $end, "10");
	var_dump($res);
	#var_dump(count($res) == 8 && $res[7] == $end ? True : False);
	$res = $cli->zrlist($end, $start, "10");
	var_dump($res);
	#var_dump(count($res) == 8 && $res[7] == $start ? True : False);
    $key = $prefix . "jk100";
    $res = $cli->jdelay("set", $key, "4", 1);
    var_dump($res);
    sleep(3);
    $res = $cli->get($key);
    var_dump($res);
}

$agent_conf = array ("hosts" => array("10.10.105.5:10024"), "read_timeout_ms" => 3000);
$ssdb_cli = SSDBCluster::GetSSDBClient($agent_conf);
$ssdb_cli->change_namespace("test");
$t = time();
$prefix = "api_test." . "$t" . "_";
$prefix_ret = "test|api_test." . "$t" . "_";
#var_dump($prefix);
echo "=========test proto ssdb client==========";
test_kv($ssdb_cli, $prefix);

sleep(1);
$t = time();
$prefix = "test|api_test." . "$t" . "_";
#var_dump($prefix);
$router_cli = new JiuyanSSDB($agent_conf);;
#$router_cli->change_namespace("test");
echo "===========test proto router============";
test_router($router_cli, $prefix);

?>
