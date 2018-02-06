<?php 

namespace BanyanDB;
use Exception;
class BanyanClient {
    public function __construct($hosts, $ns, $tab, $timeout = 3000, $retries = 3) {
        $this->hosts = $hosts;
        $this->ns = $ns;
        $this->tab = $tab;
        $this->delay_ns = "banyan";
        $this->delay_tab = "delay";
        $this->proto = "by";
        $this->timeout = $timeout;
        $this->retries = $retries;
        $this->fail_count = 0;
        $this->cmd = "";
        $this->nkey = 0;
        $this->nfield = 0;
        $this->sock = NULL;
        $this->read_buf = "";
        $this->write_buf = "";
        $this->latency = 0; //ms
        $this->read_option = "";
        $this->ngx_requestid = "";
        $this->connection();
    }

    public function __destruct() {
        $this->close();
    }

    public function change_namespace($ns) {
        $this->ns = $ns;
    }

    public function change_table($tab) {
        $this->tab = $tab;
    }

    // master or slave
    public function set_read_option($read) {
        $this->read_option = $read;
    }

    public function set_ngx_requestid($requestid) {
        $this->ngx_requestid = $requestid;
    }

    public function get_lantency_ms() {
        return $this->latency;
    }

    public function get_request_info() {
        $str = sprintf("latency: %d",$this.latency) . "\nrequest: " . $this->write_buf . "\nresponse: " . $this->read_buf;
        return $str;
    }

    public function get_options() {
        $uid = 'php_' . md5(time() . mt_rand(1,1000000));
        $options = "ns:" . $this->ns . ",tab:" . $this->tab . ",proto:by,rid:" . $uid;
        if ($this->read_option != '') {
            $options = $options . ',read:' . $this->read_option;
        }
        if ($this->ngx_requestid != '') {
            $options = $options . ',ngx:' . $this->ngx_requestid;
        }
        return $options;
    }

    protected function connection() {
        $nhost = count($this->hosts);
        $index = rand(0, $nhost - 1);
        for ($i = $index; $i < $nhost; $i++) {
            $pos = strpos($this->hosts[$i], ":", 0);
            if ($pos === FALSE) {
                continue;
            }
            $ip = substr($this->hosts[$i], 0, $pos);
            $port = substr($this->hosts[$i], $pos + 1);
            $this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_set_option($this->sock, SOL_SOCKET, SO_RCVTIMEO, 
                array('sec' => $this->timeout / 1000, 'usec' => 1000 * ($this->timeout % 1000)));
            $ret = @socket_connect($this->sock, $ip, $port);
            if ($ret === TRUE) {
                return TRUE;
            } else {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                $this->close();
                var_dump("socket_connect $ip:$port failed:$errormsg");
            }
        }
        for ($i = 0; $i < $index; $i++) {
            $pos = strpos($this->hosts[$i], ":", 0);
            if ($pos === FALSE) {
                continue;
            }
            $ip = substr($this->hosts[$i], 0, $pos);
            $port = substr($this->hosts[$i], $pos + 1);
            $this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_set_option($this->sock, SOL_SOCKET, SO_RCVTIMEO, 
                array('sec' => $this->timeout / 1000, 'usec' => 1000 * ($this->timeout % 1000)));
            $ret = @socket_connect($this->sock, $ip, $port);
            if ($ret === TRUE) {
                return TRUE;
            } else {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                $this->close();
                var_dump("socket_connect $ip:$port failed:$errormsg");
            }
        }
    
        var_dump("socket_connect all hosts failed");
        throw new Exception("connection all host failed");
        return FALSE;
    }

    protected function close() {
        if ($this->sock) {
            socket_close($this->sock);
            $this->sock = FALSE;
        }
    }

    # kv
    public function get($key) {
        return $this->request("get", array($key));
    }
    public function set($key, $val) {
        return $this->request("set", array($key, $val));
    }
    public function getset($key, $val) {
        return $this->request("getset", array($key, $val));
    }
    public function del($key) {
        return $this->request("del", array($key)); 
    }
    public function incr($key, $val = 1) {
        return $this->request("incr", array($key, $val));
    }
    public function decr($key, $val = 1) {
        return $this->request("decr", array($key, $val));
    }
    public function exists($key) {
        return $this->request("exists", array($key));
    }
    public function getbit($key, $val) {
        return $this->request("getbit", array($key, $val));
    }
    public function setbit($key, $val, $flags) {
        return $this->request("setbit", array($key, $val, $flags));
    }
    public function scan($start, $end, $limit) {
        return $this->request("scan", array($start, $end, $limit));
    }
    public function rscan($start, $end, $limit) {
        return $this->request("rscan", array($start, $end, $limit));
    }
    public function keys($start, $end, $limit) {
        return $this->request("keys", array($start, $end, $limit));
    }
    public function rkeys($start, $end, $limit) {
        return $this->request("rkeys", array($start, $end, $limit));
    }
    public function multi_get($keys) {
        return $this->request("multi_get", $keys);
    }
    public function multi_set($kvs) {
        $args = array();
        foreach($kvs as $k=>$v) {
            $args[] = $k;
            $args[] = $v;
        }
        return $this->request("multi_set", $args);
    }
    public function multi_del($keys) {
        return $this->request("multi_del", $keys);
    }

    # ttl
    public function setx($key, $val, $ttl) {
        return $this->request("setx", array($key, $val, $ttl));
    }
    public function expire($key, $ttl) {
        return $this->request("expire", array($key, $ttl));
    }

    # hash
    public function hsize($key) {
        return $this->request("hsize", array($key));
    }
    public function hget($key, $field) {
        return $this->request("hget", array($key, $field));
    }
    public function hset($key, $field, $val) {
        return $this->request("hset", array($key, $field, $val));
    }
    public function hdel($key, $field) {
        return $this->request("hdel", array($key, $field));
    }
    public function hincr($key, $field, $val = 1) {
        return $this->request("hincr", array($key, $field, $val));
    }
    public function hdecr($key, $field, $val = 1, $optoins = NULL) {
        return $this->request("hdecr", array($key, $field, $val), $optoins);
    }
    public function hgetall($key) {
        return $this->request("hgetall", array($key));
    }
    public function hclear($key) {
        return $this->request("hclear", array($key));
    }
    public function hexists($key, $field) {
        return $this->request("hexists", array($key, $field));
    }
    public function hscan($key, $start, $end, $limit) {
        return $this->request("hscan", array($key, $start, $end, $limit));
    }
    public function hrscan($key, $start, $end, $limit) {
        return $this->request("hrscan", array($key, $start, $end, $limit));
    }
    public function hkeys($key, $start, $end, $limit) {
        return $this->request("hkeys", array($key, $start, $end, $limit));
    }
    public function hrkeys($key, $start, $end, $limit) {
        return $this->request("hrkeys", array($key, $start, $end, $limit));
    }
    public function hlist($start, $end, $limit) {
        return $this->request("hlist", array($start, $end, $limit));
    }
    public function hrlist($start, $end, $limit) {
        return $this->request("hrlist", array($start, $end, $limit));
    }
    public function multi_hget($key, $fields) {
        $args = array($key);
        foreach($fields as $field) {
            $args[] = $field;
        }
        return $this->request("multi_hget", $args);
    }
    public function multi_hset($key, $kvs) {
        $args = array($key);
        foreach($kvs as $k=>$v) {
            $args[] = $k;
            $args[] = $v;
        }
        return $this->request("multi_hset", $args);
    }
    public function multi_hdel($key, $fields) {
        $args = array($key);
        foreach($fields as $field) {
            $args[] = $field;
        }
        return $this->request("multi_hdel", $args);
    }
    public function mhmget($fields, $keys) {
        $this->nkey = count($keys);
        $this->nfield = count($fields);
        $args = array();
        foreach($keys as $key) {
            $args[] = $key;
        }
        foreach($fields as $field) {
            $args[] = $field;
        }
        return $this->request("mhmget", $args);
    }

    ## zset
    public function zsize($key) {
        return $this->request("zsize", array($key));
    }
    public function zget($key, $member) {
        return $this->request("zget", array($key, $member));
    }
    public function zset($key, $member, $score) {
        return $this->request("zset", array($key, $member, $score));
    }
    public function zdel($key, $member) {
        return $this->request("zdel", array($key, $member));
    }
    public function zincr($key, $member, $score) {
        return $this->request("zincr", array($key, $member, $score));
    }
    public function zdecr($key, $member, $score) {
        return $this->request("zdecr", array($key, $member, $score));
    }
    public function zclear($key) {
        return $this->request("zclear", array($key));
    }
    public function zexists($key, $member) {
        return $this->request("zexists", array($key, $member));
    }
    public function zcount($key, $start_score, $end_score) {
        return $this->request("zcount", array($key, $start_score, $end_score));
    }
    public function zremrangebyrank($key, $start_score, $end_score) {
        return $this->request("zremrangebyrank", array($key, $start_score, $end_score));
    }
    public function zkeys($key, $member, $start_score, $end_score, $limit) {
        return $this->request("zkeys", array($key, $member, $start_score, $end_score, $limit));
    }
    public function zscan($key, $member, $start_score, $end_score, $limit) {
        return $this->request("zscan", array($key, $member, $start_score, $end_score, $limit));
    }
    public function zrscan($key, $member, $start_score, $end_score, $limit) {
        return $this->request("zrscan", array($key, $member, $start_score, $end_score, $limit));
    }
    public function zlist($start, $end, $limit) {
        return $this->request("zlist", array($start, $end, $limit));
    }
    public function zrlist($start, $end, $limit) {
        return $this->request("zrlist", array($start, $end, $limit));
    }
    public function zrange($key, $offset, $limit) {
        return $this->request("zrange", array($key, $offset, $limit));
    }
    public function zrank($key, $member) {
        return $this->request("zrank", array($key, $member));
    }
    public function zrrank($key, $member) {
        return $this->request("zrrank", array($key, $member));
    }
    public function zrrange($key, $offset, $limit) {
        return $this->request("zrrange", array($key, $offset, $limit));
    }
    public function multi_zget($key, $members) {
        $args = array($key);
        foreach ($members as $member) {
            $args[] = $member;
        }
        return $this->request("multi_zget", $args);
    }
    public function multi_zset($key, $kvs) {
        $args = array($key);
        foreach($kvs as $k=>$v) {
            $args[] = $k;
            $args[] = $v;
        }
        return $this->request("multi_zset", $args);
    }
    public function multi_zdel($key, $members) {
        $args = array($key);
        foreach ($members as $member) {
            $args[] = $member;
        }
        return $this->request("multi_zdel", $args);
    }

    ## vset
    public function vsize($key) {
        return $this->request("vsize", array($key));
    }
    public function vget($key, $member) {
        return $this->request("vget", array($key, $member));
    }
    public function vset($key, $member, $score, $val) {
        return $this->request("vset", array($key, $member, $score, $val));
    }
    public function vset_score($key, $member, $score) {
        return $this->request("vset_score", array($key, $member, $score));
    }
    public function vset_value($key, $member, $val) {
        return $this->request("vset_value", array($key, $member, $val));
    }
    public function vdel($key, $member) {
        return $this->request("vdel", array($key, $member));
    }
    public function vincr($key, $member, $score) {
        return $this->request("vincr", array($key, $member, $score));
    }
    public function vdecr($key, $member, $score) {
        return $this->request("vdecr", array($key, $member, $score));
    }
    public function vclear($key) {
        return $this->request("vclear", array($key));
    }
    public function vexists($key, $member) {
        return $this->request("vexists", array($key, $member));
    }
    public function vcount($key, $start_score, $end_score) {
        return $this->request("vcount", array($key, $start_score, $end_score));
    }
    public function vremrangebyrank($key, $start_score, $end_score) {
        return $this->request("vremrangebyrank", array($key, $start_score, $end_score));
    }
    public function vkeys($key, $member, $start_score, $end_score, $limit) {
        return $this->request("vkeys", array($key, $member, $start_score, $end_score, $limit));
    }
    public function vscan($key, $member, $start_score, $end_score, $limit) {
        return $this->request("vscan", array($key, $member, $start_score, $end_score, $limit));
    }
    public function vrscan($key, $member, $start_score, $end_score, $limit) {
        return $this->request("vrscan", array($key, $member, $start_score, $end_score, $limit));
    }
    public function vlist($start, $end, $limit) {
        return $this->request("vlist", array($start, $end, $limit));
    }
    public function vrlist($start, $end, $limit) {
        return $this->request("vrlist", array($start, $end, $limit));
    }
    public function vrange($key, $offset, $limit) {
        return $this->request("vrange", array($key, $offset, $limit));
    }
    public function vrrange($key, $offset, $limit) {
        return $this->request("vrrange", array($key, $offset, $limit));
    }
    public function multi_vget($key, $members) {
        $args = array($key);
        foreach ($members as $member) {
            $args[] = $member;
        }
        return $this->request("multi_vget", $args);
    }
    public function multi_vset($key, $kvs) {
        $args = array($key);
        foreach($kvs as $k=>$v) {
            $args[] = $k;
            $args[] = $v->score;
            $args[] = $v->val;
        }
        return $this->request("multi_vset", $args);
    }
    public function multi_vdel($key, $members) {
        $args = array($key);
        foreach ($members as $member) {
            $args[] = $member;
        }
        return $this->request("multi_vdel", $args);
    }
    
    # queue
    public function qsize($key) {
        return $this->request("qsize", array($key));
    }
    public function qpop($key, $limit = 1) {
        return $this->request("qpop", array($key, $limit));
    }
    public function qpop_front($key, $limit = 1) {
        return $this->request("qpop", array($key, $limit));
    }
    public function qpop_back($key, $limit = 1) {
        return $this->request("qpop_back", array($key, $limit));
    }
    public function qpush($key, $items) {
        $args = array($key);
        if (is_array($items)) {
            foreach ($items as $item) {
                $args[] = $item;
            }
        } else {
            $args[] = $items;
        }
        return $this->request("qpush", $args);
    }
    public function qpush_front($key, $items) {
        $args = array($key);
        foreach ($items as $item) {
            $args[] = $item;
        }
        return $this->request("qpush_front", $args);
    }
    public function qpush_back($key, $items) {
        $args = array($key);
        foreach ($items as $item) {
            $args[] = $item;
        }
        return $this->request("qpush", $args);
    }
    public function qclear($key) {
         return $this->request("qclear", array($key));
    }
    public function qlist($start, $end, $limit) {
         return $this->request("qlist", array($start, $end, $limit));
    }
    public function qrlist($start, $end, $limit) {
         return $this->request("qrlist", array($start, $end, $limit));
    }
    public function qslice($key, $begin, $end) {
         return $this->request("qslice", array($key, $begin, $end));
    }
    public function qrange($key, $begin, $limit) {
         return $this->request("qrange", array($key, $begin, $limit));
    }
    
    # jdelay 
    public function jdelay() {
        $args = func_get_args();
        $arr = array_slice($args, 0, count($args) - 1);
        $opts = $this->get_options();
        $reqs[] = $opts;
        foreach($arr as $item) {
            $reqs[] = $item;
        }
        $statements = $this->serizlize_request($reqs);
        #echo "statements $statements";
        $new_args = array();
        $new_args[] = $statements;
        $new_args[] = $args[count($args) - 1];
        #print_r($new_args);
        return $this->request("jdelay", $new_args, NULL);
    }
    public function jcancel($seq) {
        return $this->request("jcancel", array($seq), NULL);
    }
    public function jllen($key) {
        return $this->request("jllen", array($key));
    }
    public function jlpop($key) {
        return $this->request("jlpop", array($key));
    }
    public function jlpush() {
        // key seq item ttl or key item ttl
        $args = func_get_args();
        $argc = count($args);
        if ($argc == 3 or $argc == 4) {
        return $this->request("jlpush", $args);
        } else {
            throw new BanyanDBException("args is 3 or 4");
        }
    }
    public function jlslice($key, $start, $limit) {
        return $this->request("jlslice", array($key, $start, $limit));
    }
    public function jlgetd($key, $seq) {
        return $this->request("jlgetd", array($key, $seq));
    }
    public function jlsetd($key, $seq, $item) {
        return $this->request("jlsetd", array($key, $seq, $item));
    }
    public function jldeld($key, $seq) {
        return $this->request("jldeld", array($key, $seq));
    }
    public function jldel_all($key) {
        return $this->request("jldel_all", array($key));
    }
    public function jllist($start, $end, $limit) {
        return $this->request("jllist", array($start, $end, $limit));
    }

    protected function request($cmd, $params = array()) {
        $this->latency = 0;
        $this->write_buf = "";
        $this->read_buf = "";
        $uid = 'php_' . md5(time() . mt_rand(1,1000000));
        #echo $uid;
        $send_params = array();
        if (count($params) != 0) {
            $send_params = $params;
        }
        $opts = "";
        if ($cmd == "jdelay" or $cmd == "jcancel") {
            $opts = "ns:" . $this->delay_ns  . ",tab:" . $this->delay_tab . ",proto:" . $this->proto. ",rid:" . $uid;
            if ($this->read_option != '') {
                $opts = $opts . ",read:" . $this->read_option;
            }
            if ($this->ngx_requestid != '') {
                $opts = $opts . ",ngx:" . $this->ngx_requestid;
            }
            #print $opts;
        } else if ($cmd == "mhmget") {
            $opts = $this->get_options();
            $opts = $opts . ",nkey:" . $this->nkey . ",nfield:" . $this->nfield;
        } else {
            $opts = $this->get_options();
        }
        
        $reqs[] = $opts;
        $reqs[] = $cmd;
        foreach ($send_params as $item) {
            $reqs[] = $item;
        }
        #var_dump($reqs);
        $this->write_buf = $this->serizlize_request($reqs);
        $start = microtime(true);
        $this->send_buf($this->write_buf);
        $reps = $this->recv_response();
        $end = microtime(true);
        $this->latency = ($end - $start) * 1000;
        $res = NULL;
        if ($cmd == "mhmget") {
            $res = new BanyanResponse($cmd, $params, $reps, $this->nkey, $this->nfield);
        } else {
            $res =  new BanyanResponse($cmd, $params, $reps);
        }
        if ($res->ok()) {
            return $res->val;
        } else if ($res->not_found()) {
            return NULL;
        } else if ($res->buffer()) {
            return TRUE;
        } else { // cliet_error or error
            $str = "request: " . $this->write_buf . "\nresponse: " . $this->read_buf . "\nerr_msg: " . $res->error_msg;
            throw new BanyanDBException($str);
        }
    }

    protected function serizlize_request($reqs) {
        $params = array();
        foreach ($reqs as $item) {
            #$params[] = sprintf("%u", strlen($item));
            $params[] = strlen($item);
            $params[] = $item;
        }
        $sep = "\n";
        $buf = join($sep, $params) . "\n\n";
        #echo "$buf";
        return $buf;
    }

    protected function send_buf($buf) {
        while ($this->fail_count < $this->retries) {
            if ($this->tcp_send($buf)) {
                #echo "xxx=tcp_send\n";
                return True;
            } else {
                $this->fail_count += 1;
                $this->connection();
                #echo "xxx=connect\n";
            }
        }        
        return FALSE;
    }

    protected function tcp_send($buf) {
        $b = $buf;
        while (TRUE) {
            if ($this->sock === FALSE) {
                return FALSE;
            }
            $n = @socket_write($this->sock, $b);
            $b = substr($b, $n);
            if (strlen($b) == 0) {
                break;
            }
        }
        return TRUE;
    }

    protected function recv_response() {
        #echo "xxx=recv_response\n";
        while (TRUE) {
            $res = $this->parse();
            if ($res === NULL) {
                if ($this->tcp_recv() == 0) {
                    return NULL;
                }
            } else {
                return $res;
            }
        }
    }

    protected function tcp_recv() {
        $d = @socket_read($this->sock, 8192, PHP_BINARY_READ);
        if ($d === FALSE || $d === '') {
            $this->close();
            return 0;
        }

        $this->read_buf .= $d;
        return strlen($d);    
    }

    protected function parse() {
        $epos = 0;
        $epos = 0;
        $ret = array();    
        while (TRUE) {
            $spos = $epos;
            $epos = strpos($this->read_buf, "\n", $spos);
            if ($epos === FALSE) {
                break;
            }
            $epos += 1;
            $line = substr($this->read_buf, $spos, $epos - $spos);
            $spos = $epos;
            $line = trim($line);
            if (strlen($line) == 0) {
                if (count($ret) == 0) {
                    continue;
                } else {
                    $this->read_buf = substr($this->read_buf, $spos);
                    return $ret;
                }
            }
            $num = intval($line);
            $epos = ($spos + $num);
            if ($epos > strlen($this->read_buf)) {
                break;
            }  
            $data = substr($this->read_buf, $spos, $epos - $spos);
            $ret[] = $data;
            $spos = $epos;
            $n = strpos($this->read_buf, "\n", $spos);
            if ($n === FALSE) {
                break;
            } 
            $epos = $n + 1;
        }

        return NULL;
    }
}
