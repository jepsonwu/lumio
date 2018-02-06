<?php

include __DIR__.'/../../../../vendor/autoload.php';

function test_kv($cli, $prefix) {
    print "===========test kv===============\n";
    $key = $prefix . "k0";
    $val = "v0";
    $res = $cli->set($key, $val);
    if ($res != 1) {
        var_dump($res);
        throw new Exception("set");
    }
    /*if (!($res->ok() == TRUE or $res->val == 1)) {
        var_dump($res);
        throw new Exception("set");
    }*/
    $res = $cli->get($key);
    if ($res != $val) {
        throw new Exception("get");
    }
    $res = $cli->getset($key, "v1");
    if (!($res == $val)) {
        throw new Exception("getset");
    }
    $res = $cli->get($key);
    if (!($res== "v1")) {
        throw new Exception("get");
    }
    $res = $cli->del($key);
    if ($res != 1) {
        throw new Exception("del");
    }
    $res = $cli->get($key);
    if ($res != NULL) {
        throw new Exception("get");
    }
    $key = $prefix . "k1";
    $val = "888";
    $res = $cli->exists($key);
    if ($res != 0) {
        var_dump($res);
        throw new Exception("exists");
    }
    $res = $cli->incr($key, $val);
    if ($res != $val) {
        var_dump($res);
        throw new Exception("incr");
    }
    $res = $cli->decr($key, "444");
    if ($res != "444") {
        var_dump($res);
        throw new Exception("incr");
    }
    $res = $cli->exists($key);
    if ($res != 1) {
        throw new Exception("exists");
    }
    $key = $prefix . "k2";
    $res = $cli->setbit($key, "123456", "1");
    if ($res != 0) {
        throw new Exception("setbit");
    }
    $res = $cli->getbit($key, "123456");
    if ($res != 1) {
        throw new Exception("getbit");
    }
    $key1 = $prefix . "k3";
    $key2 = $prefix . "k4";
    $key3 = $prefix . "k5";
    $kvs = array($key1 => "v3", $key2 => "v4", $key3 => "v5");
    $res = $cli->multi_set($kvs);
    if ($res != 3) {
        var_dump($res);
        throw new Exception("multi_set");
    }
    $res = $cli->multi_get(array($key1, $key2, $key3));
    if (!($res[$key1] == "v3" and $res[$key2] == "v4" and $res[$key3] == "v5")) {
        throw new Exception("multi_get");
    }
    $res = $cli->multi_del(array($key1, $key2, $key3));
    if ($res != 3) {
        throw new Exception("multi_del");
    }
    $kvs = array();
    for ($i = 0; $i < 1000; ++$i) {
        $key = $prefix . sprintf("k%08u", $i);
        $val = sprintf("v%08u", $i);
        $kvs[$key] = $val;
        #array_push($kvs, array($key => $val));
    }
    $res = $cli->multi_set($kvs);
    if ($res != 1000) {
        var_dump($res);
        throw new Exception("multi_set");
    }
    $start = $prefix . sprintf("k%08u", 100);
    $end = $prefix . sprintf("k%08u", 200);
    $res = $cli->scan($start, $end, 4);
    if (count($res) != 4) {
        var_dump($res);
        throw new Exception("scan");
    }
    #var_dump($res->val);
    $res = $cli->keys($start, $end, 4);
    if (count($res) != 4) {
        throw new Exception("keys");
    }
    #var_dump($res->val);
}

function test_hash($cli, $prefix) {
    print "===========test hash===============\n";
    $key = $prefix . "hk0";
    $field = "f0";
    $val = "v0";
    $res = $cli->hset($key, $field, $val);
    if ($res != 1) {
        throw new Exception("hset");
    }
    $res = $cli->hget($key, $field);
    if ($res != $val) {
        throw new Exception("hget");
    }
    $res = $cli->hdel($key, $field);
    if ($res != 1) {
        throw new Exception("hdel");
    }
    $field = "f1";
    $val = "888";
    $res = $cli->hexists($key, $field);
    if ($res != 0) {
        throw new Exception("hexists");
    }
    $res = $cli->hincr($key, $field, $val);
    if ($res != 888) {
        throw new Exception("hincr");
    }
    $res = $cli->hdecr($key, $field, "444");
    if ($res != 444) {
        throw new Exception("hdecr");
    }
    $key = $prefix . "hk2";
    $kvs = array("f1" => "v1", "f2" => "v2", "f3" => "v3");
    $res = $cli->multi_hset($key, $kvs);
    if ($res != 3) {
        throw new Exception("multi_hset");
    }
    $res = $cli->hgetall($key);
    if (!($res["f2"] == "v2" and count($res) == 3)) {
        var_dump($res->val);
        throw new Exception("hgetall");
    }
    $res = $cli->hsize($key);
    if ($res != 3) {
        throw new Exception("hsize");
    }
    $res = $cli->multi_hget($key, array("f1", "f2", "f3"));
    if (!($res["f1"] == "v1" and count($res) == 3)) {
        var_dump($res->val);
        throw new Exception("multi_hget");
    }
    $key2 = $prefix . sprintf("hk3");
    $kvs = array("f1" => "v4", "f2" => "v5", "f3" => "v6");
    $res = $cli->multi_hset($key2, $kvs);
    if ($res != 3) {
        var_dump($res);
        throw new Exception("multi_hset");
    }
    $fields = array("f1", "f2", "f3");
    $keys = array($key, $key2);
    $res = $cli->mhmget($fields, $keys);
    if (count($res) <= 0) {
        throw new Exception("mhmget");
    }
    $res = $cli->multi_hdel($key, array("f1", "f2", "f3"));
    if ($res != 3) {
        throw new Exception("multi_hdel");
    }
    /*$key = $prefix . "hk6";
    $field = "f0";
    $val = "v0";
    $val1 = "v1";
    $res = $cli->hset($key, $field, $val);
    $res = $cli->hset_if_eq($key, $field, $val1, "v2");
    if (!($res->error() == TRUE and $res->val == $val)) {
        throw new Exception("hset_if_eq");
    }
    $res = $cli->hdel_if_eq($key, $field, "v2");
    if (!($res->error() == TRUE and $res->val == $val)) {
        throw new Exception("hdel_if_eq");
    }
    $res = $cli->hget($key, $field);
    if (!($res->ok() == TRUE and $res->val == $val)) {
        throw new Exception("hget");
    }*/
    
    $key = $prefix . "hk10";
    $kvs = array();
    for($i = 0; $i < 1000; ++$i) {
        $field = sprintf("f%08u", $i);
        $val = "kval";
        $kvs[$field] =  $val;
    }
    $res = $cli->multi_hset($key, $kvs);
    #var_dump($res);
    if ($res != 1000) {
        throw new Exception("multi_hset");
    }
    $start = sprintf("f%08u", 100);
    $end = sprintf("f%08u", 110);
    $res = $cli->hscan($key, $start, $end, '8');
    if (count($res) != 8) {
        var_dump($res);
        throw new Exception("hscan");
    }
    $start = sprintf("f%08u", 200);
    $end = sprintf("f%08u", 300);
    $res = $cli->hkeys($key, $start, $end, '88');
    if (count($res) != 88) {
        throw new Exception("hkeys");
    }
    for ($i = 0; $i < 100; $i++) {
        $key = $prefix . sprintf("h%08u", $i);
        $cli->hset($key, "f", "v");
    }
    $start = $prefix . sprintf("h%08u", 10);
    $end = $prefix . sprintf("h%08u", 20);
    $res = $cli->hlist($start, $end, "8");
    if (count($res) != 8) {
        var_dump($res);
        throw new Exception("hlist");
    }
}

function test_zset($cli, $prefix) {
    print "===========test zset==============\n";
    $key = $prefix . "zk0";
    $member = "m0";
    $val = "88";
    $res = $cli->zset($key, $member, $val);
    if ($res != 1) {
        throw new Exception("zset");
    }
    $res = $cli->zget($key, $member);
    if ($res != $val) {
        throw new Exception("zget");
    }
    $res = $cli->zdel($key, $member);
    if ($res != 1) {
        throw new Exception("zdel");
    }
    $key = $prefix . "zk1";
    $member = "f1";
    $score = "888";
    $res = $cli->zexists($key, $member);
    if ($res !== 0) {
        throw new Exception("zexists");
    }
    $res = $cli->zincr($key, $member, $score);
    if ($res != 888) {
        throw new Exception("zincr");
    }
    $res = $cli->zdecr($key, $member, "444");
    if ($res != 444) {
        throw new Exception("zdecr");
    }
    $key = $prefix . "zk2";
    $res = $cli->zset($key, "m1", "1");
    $res = $cli->zset($key, "m2", "2");
    $res = $cli->zset($key, "m3", "3");
    $res = $cli->zsize($key);
    if ($res != 3) {
        throw new Exception("zsize");
    }
    $res = $cli->zclear($key);
    if ($res != 3) {
        throw new Exception("zclear");
    }
    $key = $prefix . "zk3";
    $res = $cli->multi_zset($key, array("m0" => "0", "m1" => "1", "m2" => "2"));
    if ($res != 3) {
        throw new Exception("multi_zset");
    }
    $res = $cli->multi_zget($key, array("m0", "m1", "m2"));
    if (count($res) != 3) {
        throw new Exception("multi_zget");
    }
    $res = $cli->multi_zdel($key, array("m0", "m1", "m2"));
    if ($res != 3) {
        throw new Exception("multi_zdel");
    }
    $key = $prefix . "zk10";
    $kvs = array();
    for ($i = 0; $i < 1000; ++$i) {
        $member = sprintf("m%08u", $i);
        $score = sprintf("%u", $i);
        $kvs[$member] = $score;
    }
    $res = $cli->multi_zset($key, $kvs);
    if ($res != 1000) {
        throw new Exception("multi_zset");
    }
    $res = $cli->zcount($key, "100", "200");
    if ($res != 101) {
        throw new Exception("zcount");
    }
    $member = sprintf("m%08u", 100);
    $res = $cli->zkeys($key, $member, "100", "110", "8");
    if (count($res) != 8) {
        var_dump($res);
        throw new Exception("zkeys");
    }
    $res = $cli->zscan($key, $member, "100", "110", "8");
    if (count($res) != 8) {
        var_dump($res);
        throw new Exception("zscan");
    }
    $res = $cli->zrange($key, "100", "10");
    if (count($res) != 10) {
        var_dump($res);
        throw new Exception("zrange");
    }
    for ($i = 0; $i < 1000; ++$i) {
        $key = $prefix . sprintf("z%08u", $i);
        $cli->zset($key, "m", "1");
    }
    $start = $prefix . sprintf("z%08u", 100);
    $end = $prefix . sprintf("z%08u", 200);
    $res = $cli->zlist($start, $end, "80");
    if (count($res) != 80) {
        var_dump($res);
        throw new Exception("zlist");
    }
}

function test_vset($cli, $prefix) {
    print "===========test vset==============\n";
    $key = $prefix . "vk0";
    $member = "m0";
    $score = 88;
    $val = "xx";
    $res = $cli->vset($key, $member, $score, $val);
    if ($res != 1) {
        throw new Exception("vset");
    }
    $res = $cli->vget($key, $member);
    if ($res->score != $score or $res->val != $val) {
        throw new Exception("vget");
    }
    $res = $cli->vdel($key, $member);
    if ($res != 1) {
        throw new Exception("vdel");
    }
    $key = $prefix . "vk1";
    $member = "f1";
    $score = "888";
    $res = $cli->vexists($key, $member);
    if ($res !== 0) {
        throw new Exception("vexists");
    }
    $res = $cli->vincr($key, $member, $score);
    if ($res != 888) {
        throw new Exception("vincr");
    }
    $res = $cli->vdecr($key, $member, "444");
    if ($res != 444) {
        throw new Exception("vdecr");
    }
    $key = $prefix . "vk2";
    $res = $cli->vset($key, "m1", "1", $val);
    $res = $cli->vset($key, "m2", "2", $val);
    $res = $cli->vset($key, "m3", "3", $val);
    $res = $cli->vsize($key);
    if ($res != 3) {
        throw new Exception("vsize");
    }
    $res = $cli->vclear($key);
    if ($res != 3) {
        throw new Exception("vclear");
    }
    $key = $prefix . "vk3";
    $res = $cli->multi_vset($key, array("m0" => new VSet(0, "xx"), "m1" => new VSet(1, "xx"), "m2" => new VSet(2, "xx")));
    if ($res != 3) {
        throw new Exception("multi_vset");
    }
    $res = $cli->multi_vget($key, array("m0", "m1", "m2"));
    if (count($res) != 3) {
        throw new Exception("multi_vget");
    }
    $res = $cli->multi_vdel($key, array("m0", "m1", "m2"));
    if ($res != 3) {
        throw new Exception("multi_vdel");
    }
    $key = $prefix . "vk10";
    $kvs = array();
    for ($i = 0; $i < 1000; ++$i) {
        $member = sprintf("m%08u", $i);
        $score = sprintf("%u", $i);
        $vset = new VSet(intval($score), "x");
        $kvs[$member] = $vset;
    }
    $res = $cli->multi_vset($key, $kvs);
    if ($res != 1000) {
        throw new Exception("multi_vset");
    }
    $res = $cli->vcount($key, "100", "200");
    if ($res != 101) {
        throw new Exception("vcount");
    }
    $member = sprintf("m%08u", 100);
    $res = $cli->vkeys($key, $member, "100", "110", "8");
    if (count($res) != 8) {
        var_dump($res);
        throw new Exception("zkeys");
    }
    $res = $cli->vscan($key, $member, "100", "110", "8");
    if (count($res) != 8) {
        var_dump($res);
        throw new Exception("vscan");
    }
    $res = $cli->vrange($key, "100", "10");
    if (count($res) != 10) {
        var_dump($res);
        throw new Exception("vrange");
    }
    for ($i = 0; $i < 1000; ++$i) {
        $key = $prefix . sprintf("v%08u", $i);
        $cli->vset($key, "m", "1", "xxx");
    }
    $start = $prefix . sprintf("v%08u", 100);
    $end = $prefix . sprintf("v%08u", 200);
    $res = $cli->vlist($start, $end, "80");
    if (count($res) != 80) {
        var_dump($res);
        throw new Exception("vlist");
    }
}

function test_queue($cli, $prefix) {
    print "===========test queue==============\n";
    $key = $prefix . sprintf("qk0");
    for ($i = 0; $i < 100; ++$i) {
        $val = sprintf("%u", $i);
        $res = $cli->qpush($key, array($val));
        if ($res < 1) {
            var_dump($res);
            throw new Exception("qpush");
        }
    }
    $res = $cli->qsize($key);
    if ($res != 100) {
        throw new Exception("qsize");
    }
    $res = $cli->qpop($key);
    if ($res[0] != "0") {
        var_dump($res);
        throw new Exception("qpop");
    }
    $res = $cli->qclear($key);
    if ($res <= 0) {
        throw new Exception("qclear");
    }
    $items = array();
    for ($i = 0; $i < 1000; ++$i) {
        $item = sprintf("%u", $i);
        $items[] = $item;
    }
    $res = $cli->qpush($key, $items);
    if ($res != 1000) {
        throw new Exception("qpush");
    }
    $res = $cli->qpush($key, "xxxx");
    if ($res != 1001) {
        throw new Exception("qpush");
    }
    $res = $cli->qslice($key, "100", "200");
    if (count($res) < 1) {
        throw new Exception("qslice");
    }
    $res = $cli->qrange($key, "800", "100");
    if (count($res) < 1) {
        var_dump($res);
        throw new Exception("qrange");
    }
    for ($i = 0; $i < 1000; ++$i) {
        $key = $prefix . sprintf("q%08u", $i);
        $res = $cli->qpush($key, array("item"));
    }
    $start = $prefix . sprintf("q%08u", 100);
    $end = $prefix . sprintf("q%08u", 200);
    $res = $cli->qlist($start, $end, "80");
    if (count($res) != 80) {
        var_dump($res);
        throw new Exception("qlist");
    }
}

function test_jlist($cli, $prefix) {
    print "===========test jlist==============\n";
    $key = $prefix . sprintf("jlk0");
    for ($i = 0; $i < 100; ++$i) {
        $val = sprintf("%u", $i);
        $res = $cli->jlpush($key, $val, 0);
        if ($res < 1) {
            var_dump($res);
            throw new Exception("jlpush");
        }
    }
    //$res = $cli->jlpush("xx", "x");
    $res = $cli->jlgetd($key, 1001);
    var_dump($res);
    $res = $cli->jlslice($key, 0, 10);
    if (count($res) != 10) {
        throw new Exception("jlslice");
    }
    $res = $cli->jllen($key);
    if ($res != 100) {
        throw new Exception("jllen");
    }
    $res = $cli->jldel_all($key);
    if ($res <= 0) {
        throw new Exception("jldel_all");
    }
}

function test_jdelay($cli, $prefix) {
    $key = $prefix . "k_jdelay";
    $val = "jv5";
    $res = $cli->jdelay("set", $key, $val, "1");
    if ($res < 0) {
        throw new Exception("jdelay");
    }
    sleep(3);
    $res = $cli->get($key);
    if ($res != $val) {
        var_dump($res);
        throw new Exception("get === jdelay");
    }
    $key = $prefix . "k_jdelay2";
    $res = $cli->jdelay("set", $key, "jv3", "1");
    if ($res < 0) {
        throw new Exception("jdelay");
    }
    $res = $cli->jcancel($res);
    if ($res != 1) {
        throw new Exception("jdelay");
    }
    $res = $cli->get($key);
    if ($res != NULL) {
        throw new Exception("get === jcancel");
    }
}

$t = time();
$prefix = "agent_" . "$t" . "_";
#$cli = new BanyanClient(array("10.10.105.5:1020", "10.10.105.5:10200"), "test", "api_test");
#$cli = new BanyanClient(array("10.10.105.5:10200"), "test", "api_test");
#$cli->set_read_option("master");
$conf = array(
    "hosts" => array(
        "10.10.105.5:10100",
        "10.10.105.5:10200",
    ),
    "read_timeout_ms" => 3000,
    "max_request_retry" => 1,
    "retry_on_writes" => 1,
);
$cli = BanyanDBCluster::GetBanyanClient($conf, "test", "api_test");
$cli->set_read_option("master");
#$cli = new BanyanClient(array("10.10.105.5:10424"), "test", "api_test");

test_kv($cli, $prefix);
test_hash($cli, $prefix);
test_zset($cli, $prefix);
test_vset($cli, $prefix);
test_queue($cli, $prefix);
test_jlist($cli, $prefix);
test_jdelay($cli, $prefix);

?>
