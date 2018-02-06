<?php
/**
 * Copyright (c) 2012, ideawu
 * All rights reserved.
 * @author: ideawu
 * @link: http://www.ideawu.com/
 *
 * SSDB PHP client SDK.
 */

class SSDBException extends Exception
{
}

class SSDBTimeoutException extends SSDBException
{
}

/**
 * All methods(except *exists) returns false on error,
 * so one should use Identical(if($ret === false)) to test the return value.
 */
class SimpleSSDB extends SSDB
{
  function __construct($host, $port, $timeout_ms=2000){
    parent::__construct($host, $port, $timeout_ms);
    $this->easy();
  }
}

/**
 * SSDB Client: load balancing & retry on backend failures
 */
class JiuyanSSDB extends SSDB
{
  protected $config;
  protected $read_timeout_ms = 50;
  protected $max_request_retry = 1;
  protected $retry_on_writes = 1;

  protected $valid_hosts;
  protected $invalid_hosts;
  protected $current_host;
  protected $socket;

  protected $cmd;
  protected $request;
  protected $namespace;

  function __construct($ssdb_conf, $namespace="GLOBAL") {
    $this->config = $ssdb_conf;
    $this->valid_hosts = array();
    $this->invalid_hosts = array();
    $this->current_host = -1;
    $this->socket = NULL;
    $this->namespace = $namespace;

    if (isset($this->config['hosts']) ) {
      foreach ($this->config['hosts'] as $h) {
        array_push($this->valid_hosts, $h);
      }
    }

    if (isset($this->config['read_timeout_ms']) ) {
      $this->read_timeout_ms = $this->config['read_timeout_ms'];
    }

    if (isset($this->config['max_request_retry']) ) {
      $this->max_request_retry = $this->config['max_request_retry'];
    }

    if (isset($this->config['retry_on_writes']) ) {
      $this->retry_on_writes = $this->config['retry_on_writes'];
    }

    $this->make_socket();
    $this->easy();
  }

  public function __destruct() {
    $this->close_socket();
  }

  public function get_namespace() {
    return $this->namespace;
  }

  public function change_namespace($namespace) {
    $this->namespace = $namespace;
  }

  public function change_easy($xx) {
    $this->_easy = $xx;
  }

  public function mhmget($name_arr, $key_arr) {
    $key_cnt = count($key_arr);
    $args = array_merge(array("$key_cnt"), $key_arr, $name_arr);
    return $this->__call("mhmget", $args);
  }

  public function mzrange($name_arr, $offset, $limit) {
    $name_cnt = count($name_arr);
    $args = array_merge(array("$name_cnt"), $name_arr, array("$offset"), array("$limit"));
    return $this->__call("mzrange", $args);
  }

  protected function make_socket() {
    if (NULL == $this->socket) {
      $valid_count = count($this->valid_hosts);
      if ($valid_count <= 0) {
        $this->valid_hosts = array_values($this->invalid_hosts);
        $this->invalid_hosts = array();
        $valid_count = count($this->valid_hosts);
      }

      if ($valid_count <= 0) {
        throw SSDBException("No SSDB hosts found.");
      }

      while ($this->socket == NULL && $valid_count > 0) {
        $this->current_host = rand(0, $valid_count - 1);
        $this->socket = @stream_socket_client($this->valid_hosts[$this->current_host], $errno, $errstr, 
          (float)$this->read_timeout_ms/1000);
        if (!$this->socket) {
          $this->close_socket();
          $valid_count = count($this->valid_hosts);
        } else {
          $timeout_sec = 0;
          $timeout_usec = $this->read_timeout_ms * 1000;
          if ($this->read_timeout_ms >= 1000) {
            $timeout_sec = (int)$this->read_timeout_ms / 1000;
            $timeout_usec = ((int)$this->read_timeout_ms % 1000) * 1000;
          }
          @stream_set_timeout($this->sock, $timeout_sec, $timeout_usec);
        }
      }
    }

    return $this->socket;
  }

  protected function close_socket() {
    if ($this->socket) {
      @fclose($this->socket);
      $this->socket = NULL;
    }

    if ($this->current_host != -1) {
      //error_log("close connection to: " . $this->valid_hosts[$this->current_host]);
      array_push($this->invalid_hosts, $this->valid_hosts[$this->current_host]);
      unset($this->valid_hosts[$this->current_host]);
      $this->valid_hosts = array_values($this->valid_hosts);
      $this->current_host = -1;
    }
  }

  protected function send($data) {
    $ps = array();
    foreach ($data as $p) {
      $ps[] = strlen($p);
      $ps[] = $p;
    }

    if ($this->namespace == "GLOBAL") {
      $s =  join("\n", $ps) . "\n\n";
    } else {
      $s = "" . strlen("ns:$this->namespace") . "\nns:$this->namespace" . "\n" . join("\n", $ps) . "\n\n";
    }

    $this->cmd = $data[0];
    $this->request = $s;
    $this->make_socket();

    return $this->do_send();
  }

  protected function do_send() {
    $s = $this->request;
    while (true) {
      $ret = @fwrite($this->socket, $s);
      if ($ret == false) {
        $s = $this->request;
        $this->close_socket();
        $this->make_socket();
        continue;
      }
      $s = substr($s, $ret);
      if (@fflush($this->socket) == false) {
        $s = $this->request;
        $this->close_socket();
        $this->make_socket();
        continue;
      }
      if (@strlen($s) == 0) {
        break;
      }
    }

    return true;
  }

  protected function recv($req_retry = 0) {
    $this->step = self::STEP_SIZE;

    while (true) {
      $ret = $this->parse();
      if ($ret === null) {
        $data = @fread($this->socket, 1024 * 1024);
        if ($data === false || $data === '') {
          $this->close_socket();
          if (!$this->retry_on_writes) {
            $write_cmd_patterms = array("incr", "decr", "set", "push", "pop", "put", "expire", "clear", "rem", "trim", "fix");
            foreach($write_cmd_patterms as $pt) {
              if (strstr($this->cmd, $pt) ) {
                // do not retry on write requests if $this->retry_on_writes is not set
                throw new SSDBException("Receive from SSDB service/router. Timeout!. rd: "
                  . " cmd: " . $this->cmd . ", request: \n" . $this->request);
              }
            }
          }
          // retry on read requests or $this->retry_on_writes is set 
          if ($req_retry <= $this->max_request_retry) {
            $this->make_socket();
            $this->do_send();
            return $this->recv($req_retry + 1);
          } else {
            throw new SSDBException("req_retry reach max times");
          }
        }
        $this->recv_buf .= $data;
      } else {
        return $ret;
      }
    }
  }
}

class SSDBCluster {
  private static $_ssdb_client;

  private function __construct() {
  }
  private function __clone() {
  }

  public static function GetSSDBClient($conf = array()) {
    if (!SSDBCluster::$_ssdb_client instanceof JiuyanSSDB) {
      SSDBCluster::$_ssdb_client = new JiuyanSSDB($conf);
    }

    return SSDBCluster::$_ssdb_client;
  }

  public static function DestroySSDBClient() {
    if (SSDBCluster::$_ssdb_client instanceof JiuyanSSDB) {
      SSDBCluster::$_ssdb_client->close_socket();
    }

    SSDBCluster::$_ssdb_client = NULL;
  }
}

class SSDB_Response
{
  public $cmd;
  public $code;
  public $data = null;
  public $message;

  function __construct($code='ok', $data_or_message=null){
    $this->code = $code;
    if($code == 'ok'){
      $this->data = $data_or_message;
    }else{
      $this->message = $data_or_message;
    }
  }

  function __toString(){
    if($this->code == 'ok'){
      $s = $this->data === null? '' : json_encode($this->data);
    }else{
      $s = $this->message;
    }
    return sprintf('%-13s %12s %s', $this->cmd, $this->code, $s);
  }

  function ok(){
    return $this->code == 'ok';
  }

  function not_found(){
    return $this->code == 'not_found';
  }
}

class SSDB
{
  protected $debug = false;
  public $sock = null;
  protected $_closed = false;
  protected $recv_buf = '';
  protected $_easy = false;
  public $last_resp = null;

  function __construct($host, $port, $timeout_ms=2000){
    $timeout_f = (float)$timeout_ms/1000;
    $this->sock = @stream_socket_client("$host:$port", $errno, $errstr, $timeout_f);
    if(!$this->sock){
      throw new SSDBException("$errno: $errstr");
    }
    $timeout_sec = intval($timeout_ms/1000);
    $timeout_usec = ($timeout_ms - $timeout_sec * 1000) * 1000;
    @stream_set_timeout($this->sock, $timeout_sec, $timeout_usec);
    if(function_exists('stream_set_chunk_size')){
      @stream_set_chunk_size($this->sock, 1024 * 1024);
    }
  }

  /**
   * After this method invoked with yesno=true, all requesting methods
   * will not return a SSDB_Response object.
   * And some certain methods like get/zget will return false
   * when response is not ok(not_found, etc)
   */
  function easy(){
    $this->_easy = true;
  }

  function close(){
    if(!$this->_closed){
      @fclose($this->sock);
      $this->_closed = true;
      $this->sock = null;
    }
  }

  function closed(){
    return $this->_closed;
  }

  protected $batch_mode = false;
  protected $batch_cmds = array();

  function batch(){
    $this->batch_mode = true;
    $this->batch_cmds = array();
    return $this;
  }

  function multi(){
    return $this->batch();
  }

  function exec(){
    $ret = array();
    foreach($this->batch_cmds as $op){
      list($cmd, $params) = $op;
      $this->send_req($cmd, $params);
    }
    foreach($this->batch_cmds as $op){
      list($cmd, $params) = $op;
      $resp = $this->recv_resp($cmd, $params);
      $resp = $this->check_easy_resp($cmd, $resp);
      $ret[] = $resp;
    }
    $this->batch_mode = false;
    $this->batch_cmds = array();
    return $ret;
  }

  function request(){
    $args = func_get_args();
    $cmd = array_shift($args);
    return $this->__call($cmd, $args);
  }

  protected $async_auth_password = null;

  function auth($password){
    $this->async_auth_password = $password;
    return null;
  }

  function __call($cmd, $params=array()){
    $cmd = strtolower($cmd);
    if($this->async_auth_password !== null){
      $pass = $this->async_auth_password;
      $this->async_auth_password = null;
      $auth = $this->__call('auth', array($pass));
      if($auth !== true){
        throw new Exception("Authentication failed");
      }
    }

    if($this->batch_mode){
      $this->batch_cmds[] = array($cmd, $params);
      return $this;
    }

    try{
      if($this->send_req($cmd, $params) === false){
        $resp = new SSDB_Response('error', 'send error');
      }else{
        $resp = $this->recv_resp($cmd, $params);
      }
    }catch(SSDBException $e){
      if($this->_easy){
        throw $e;
      }else{
        $resp = new SSDB_Response('error', $e->getMessage());
      }
    }

    if($resp->code == 'noauth'){
      $msg = $resp->message;
      throw new Exception($msg);
    }

    $resp = $this->check_easy_resp($cmd, $resp);
    return $resp;
  }

  protected function check_easy_resp($cmd, $resp){
    $this->last_resp = $resp;
    if($this->_easy){
      if($resp->not_found()){
        return NULL;
      }else if(!$resp->ok() && !is_array($resp->data)){
        return false;
      }else{
        return $resp->data;
      }
    }else{
      $resp->cmd = $cmd;
      return $resp;
    }
  }

  function multi_set($kvs=array()){
    $args = array();
    foreach($kvs as $k=>$v){
      $args[] = $k;
      $args[] = $v;
    }
    return $this->__call(__FUNCTION__, $args);
  }

  function multi_hset($name, $kvs=array()){
    $args = array($name);
    foreach($kvs as $k=>$v){
      $args[] = $k;
      $args[] = $v;
    }
    return $this->__call(__FUNCTION__, $args);
  }

  function multi_zset($name, $kvs=array()){
    $args = array($name);
    foreach($kvs as $k=>$v){
      $args[] = $k;
      $args[] = $v;
    }
    return $this->__call(__FUNCTION__, $args);
  }

  function incr($key, $val=1){
    $args = func_get_args();
    return $this->__call(__FUNCTION__, $args);
  }

  function decr($key, $val=1){
    $args = func_get_args();
    return $this->__call(__FUNCTION__, $args);
  }

  function zincr($name, $key, $score=1){
    $args = func_get_args();
    return $this->__call(__FUNCTION__, $args);
  }

  function zdecr($name, $key, $score=1){
    $args = func_get_args();
    return $this->__call(__FUNCTION__, $args);
  }

  function zadd($key, $score, $value){
    $args = array($key, $value, $score);
    return $this->__call('zset', $args);
  }

  function zRevRank($name, $key){
    $args = func_get_args();
    return $this->__call("zrrank", $args);
  }

  function zRevRange($name, $offset, $limit){
    $args = func_get_args();
    return $this->__call("zrrange", $args);
  }

  function hincr($name, $key, $val=1){
    $args = func_get_args();
    return $this->__call(__FUNCTION__, $args);
  }

  function hdecr($name, $key, $val=1){
    $args = func_get_args();
    return $this->__call(__FUNCTION__, $args);
  }

  protected function send_req($cmd, $params){
    $req = array($cmd);
    foreach($params as $p){
      if(is_array($p)){
        $req = array_merge($req, $p);
      }else{
        $req[] = $p;
      }
    }
    return $this->send($req);
  }

  protected function recv_resp($cmd, $params){
    $resp = $this->recv();
    if($resp === false){
      return new SSDB_Response('error', 'Unknown error');
    }else if(!$resp){
      return new SSDB_Response('disconnected', 'Connection closed');
    }
    if($resp[0] == 'noauth'){
      $errmsg = isset($resp[1])? $resp[1] : '';
      return new SSDB_Response($resp[0], $errmsg);
    }
    switch($cmd){
    case 'dbsize':
    case 'ping':
    case 'qset':
    case 'getbit':
    case 'setbit':
    case 'countbit':
    case 'strlen':
    case 'set':
    case 'setx':
    case 'setnx':
    case 'zset':
    case 'hset':
    case 'qpush':
    case 'qpush_front':
    case 'qpush_back':
    case 'qtrim_front':
    case 'qtrim_back':
    case 'del':
    case 'zdel':
    case 'hdel':
    case 'hsize':
    case 'zsize':
    case 'qsize':
    case 'hclear':
    case 'zclear':
    case 'qclear':
    case 'multi_set':
    case 'multi_del':
    case 'multi_hset':
    case 'multi_hdel':
    case 'multi_zset':
    case 'multi_zdel':
    case 'incr':
    case 'decr':
    case 'zincr':
    case 'zdecr':
    case 'hincr':
    case 'hdecr':
    case 'zget':
    case 'zrank':
    case 'zrrank':
    case 'zcount':
    case 'zsum':
    case 'zremrangebyrank':
    case 'zremrangebyscore':
      if($resp[0] == 'ok'){
        $val = isset($resp[1])? intval($resp[1]) : 0;
        return new SSDB_Response($resp[0], $val);
      }else{
        $errmsg = isset($resp[1])? $resp[1] : '';
        return new SSDB_Response($resp[0], $errmsg);
      }
    case 'zavg':
      if($resp[0] == 'ok'){
        $val = isset($resp[1])? floatval($resp[1]) : (float)0;
        return new SSDB_Response($resp[0], $val);
      }else{
        $errmsg = isset($resp[1])? $resp[1] : '';
        return new SSDB_Response($resp[0], $errmsg);
      }
    case 'get':
    case 'substr':
    case 'getset':
    case 'hget':
    case 'qget':
    case 'qfront':
    case 'qback':
      if($resp[0] == 'ok'){
        if(count($resp) == 2){
          return new SSDB_Response('ok', $resp[1]);
        }else{
          return new SSDB_Response('server_error', 'Invalid response');
        }
      }else{
        $errmsg = isset($resp[1])? $resp[1] : '';
        return new SSDB_Response($resp[0], $errmsg);
      }
      break;
    case 'qpop':
    case 'qpop_front':
    case 'qpop_back':
      if($resp[0] == 'ok'){
        $size = 1;
        if(isset($params[1])){
          $size = intval($params[1]);
        }
        if($size <= 1){
          if(count($resp) == 2){
            return new SSDB_Response('ok', $resp[1]);
          }else{
            return new SSDB_Response('server_error', 'Invalid response');
          }
        }else{
          $data = array_slice($resp, 1);
          return new SSDB_Response('ok', $data);
        }
      }else{
        $errmsg = isset($resp[1])? $resp[1] : '';
        return new SSDB_Response($resp[0], $errmsg);
      }
      break;
    case 'keys':
    case 'zkeys':
    case 'hkeys':
    case 'hlist':
    case 'zlist':
    case 'qslice':
      if($resp[0] == 'ok'){
        $data = array();
        if($resp[0] == 'ok'){
          $data = array_slice($resp, 1);
        }
        return new SSDB_Response($resp[0], $data);
      }else{
        $errmsg = isset($resp[1])? $resp[1] : '';
        return new SSDB_Response($resp[0], $errmsg);
      }
    case 'auth':
    case 'exists':
    case 'hexists':
    case 'zexists':
      if($resp[0] == 'ok'){
        if(count($resp) == 2){
          return new SSDB_Response('ok', (bool)$resp[1]);
        }else{
          return new SSDB_Response('server_error', 'Invalid response');
        }
      }else{
        $errmsg = isset($resp[1])? $resp[1] : '';
        return new SSDB_Response($resp[0], $errmsg);
      }
      break;
    case 'multi_exists':
    case 'multi_hexists':
    case 'multi_zexists':
      if($resp[0] == 'ok'){
        if(count($resp) % 2 == 1){
          $data = array();
          for($i=1; $i<count($resp); $i+=2){
            $data[$resp[$i]] = (bool)$resp[$i + 1];
          }
          return new SSDB_Response('ok', $data);
        }else{
          return new SSDB_Response('server_error', 'Invalid response');
        }
      }else{
        $errmsg = isset($resp[1])? $resp[1] : '';
        return new SSDB_Response($resp[0], $errmsg);
      }
      break;
    case 'scan':
    case 'rscan':
    case 'zscan':
    case 'zrscan':
    case 'zrange':
    case 'zrrange':
    case 'hscan':
    case 'hrscan':
    case 'hgetall':
    case 'multi_hsize':
    case 'multi_zsize':
    case 'multi_get':
    case 'multi_hget':
    case 'multi_zget':
    case 'jlsliced':
      if($resp[0] == 'ok'){
        if(count($resp) % 2 == 1){
          $data = array();
          for($i=1; $i<count($resp); $i+=2){
            if($cmd[0] == 'z'){
              $data[$resp[$i]] = intval($resp[$i + 1]);
            }else{
              $data[$resp[$i]] = $resp[$i + 1];
            }
          }
          return new SSDB_Response('ok', $data);
        }else{
          return new SSDB_Response('server_error', 'Invalid response');
        }
      }else{
        $errmsg = isset($resp[1])? $resp[1] : '';
        return new SSDB_Response($resp[0], $errmsg);
      }
      break;
    case 'mhmget':
      if ($resp[0] == 'ok') {
        $key_arr = array_slice($params, 1, $params[0]);
        $name_arr = array_slice($params, 1 + $params[0]);
        $offset = 1;
        $result_table = array();
        foreach ($name_arr as $name) {
          $name_res = array();
          foreach ($key_arr as $key) {
            $name_res[$key] = $resp[$offset];
            ++$offset;
          }
          $result_table[$name] = $name_res;
        }
        return new SSDB_Response($resp[0], $result_table);
      } else {
        $errmsg = isset($resp[1])? $resp[1] : '';
        return new SSDB_Response($resp[0], $errmsg);
      }
      break;
    case 'mzrange':
      if ($resp[0] == 'ok') {
        $name_arr = array_slice($params, 1, $params[0]);
        $offset = 2;
        $result_table = array();
        foreach ($name_arr as $name) {
          $name_res = array();
          $kv_cnt = $resp[$offset] * 2;
          $kv_arr = array_slice($resp, $offset + 1, $kv_cnt);
          for ($i = 0; $i < $kv_cnt; $i += 2) {
            $name_res[$kv_arr[$i] ] = $kv_arr[$i + 1];
          }
          $offset += ($kv_cnt + 1);
          $result_table[$name] = $name_res;
        }
        return new SSDB_Response($resp[0], $result_table);
      } else {
        $errmsg = isset($resp[1])? $resp[1] : '';
        return new SSDB_Response($resp[0], $errmsg);
      }
      break;
    default:
      return new SSDB_Response($resp[0], array_slice($resp, 1));
    }
    return new SSDB_Response('error', 'Unknown command: $cmd');
  }

  protected function send($data){
    $ps = array();
    foreach($data as $p){
      $ps[] = strlen($p);
      $ps[] = $p;
    }
    $s = join("\n", $ps) . "\n\n";
    if($this->debug){
      echo '> ' . str_replace(array("\r", "\n"), array('\r', '\n'), $s) . "\n";
    }
    try{
      while(true){
        $ret = @fwrite($this->sock, $s);
        if($ret === false){
          $this->close();
          throw new SSDBException('Connection lost');
        }
        $s = substr($s, $ret);
        if(strlen($s) == 0){
          break;
        }
        @fflush($this->sock);
      }
    }catch(Exception $e){
      $this->close();
      throw new SSDBException($e->getMessage());
    }
    return $ret;
  }

  protected function recv(){
    $this->step = self::STEP_SIZE;
    while(true){
      $ret = $this->parse();
      if($ret === null){
        try{
          $data = @fread($this->sock, 1024 * 1024);
          if($this->debug){
            echo '< ' . str_replace(array("\r", "\n"), array('\r', '\n'), $data) . "\n";
          }
        }catch(Exception $e){
          $data = '';
        }
        if($data === false || $data === ''){
          if(feof($this->sock)){
            $this->close();
            throw new SSDBException('Connection lost');
          }else{
            throw new SSDBTimeoutException('Connection timeout');
          }
        }
        $this->recv_buf .= $data;
        #				echo "read " . strlen($data) . " total: " . strlen($this->recv_buf) . "\n";
      }else{
        return $ret;
      }
    }
  }

  const STEP_SIZE = 0;
  const STEP_DATA = 1;
  public $resp = array();
  public $step;
  public $block_size;

  protected function parse(){
    $spos = 0;
    $epos = 0;
    $buf_size = strlen($this->recv_buf);
    // performance issue for large reponse
    //$this->recv_buf = ltrim($this->recv_buf);
    while(true){
      $spos = $epos;
      if($this->step === self::STEP_SIZE){
        $epos = strpos($this->recv_buf, "\n", $spos);
        if($epos === false){
          break;
        }
        $epos += 1;
        $line = substr($this->recv_buf, $spos, $epos - $spos);
        $spos = $epos;

        $line = trim($line);
        if(strlen($line) == 0){ // head end
          $this->recv_buf = substr($this->recv_buf, $spos);
          $ret = $this->resp;
          $this->resp = array();
          return $ret;
        }
        $this->block_size = intval($line);
        $this->step = self::STEP_DATA;
      }
      if($this->step === self::STEP_DATA){
        $epos = $spos + $this->block_size;
        if($epos <= $buf_size){
          $n = strpos($this->recv_buf, "\n", $epos);
          if($n !== false){
            $data = substr($this->recv_buf, $spos, $epos - $spos);
            $this->resp[] = $data;
            $epos = $n + 1;
            $this->step = self::STEP_SIZE;
            continue;
          }
        }
        break;
      }
    }

    // packet not ready
    if($spos > 0){
      $this->recv_buf = substr($this->recv_buf, $spos);
    }
    return null;
  }
}
