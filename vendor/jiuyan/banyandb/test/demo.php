<?php

include("./banyan_api.php");

$t = time();
$prefix = "agent_" . "$t" . "_";
$conf = array(
    "hosts" => array(
        "10.10.105.5:10100",
        "10.10.105.5:10200",
    ),
    "read_timeout_ms" => 50,
    "max_request_retry" => 1,
    "retry_on_writes" => 1,
);
$cli = BanyanDBCluster::GetBanyanClient($conf, "test", "api_test");
$cli->set_read_option("master");
$key = $prefix . "k0";
$val = "v0";
$res = $cli->set($key, $val);
var_dump($res);
if ($res != 1) {
    var_dump($res);
    throw new Exception("set");
}
$res = $cli->get($key);
var_dump($res);
if ($res != $val) {
    throw new Exception("get");
}
?>
