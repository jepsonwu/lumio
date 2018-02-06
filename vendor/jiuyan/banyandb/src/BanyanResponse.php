<?php 

namespace BanyanDB;

class BanyanResponse {

    const _BANYAN_RESPONSE_OK = "ok";
    const _BANYAN_RESPONSE_NOT_FOUND = "not_found";
    const _BANYAN_RESPONSE_BUFFER = "buffer";
    const _BANYAN_RESPONSE_ERROR = "error";
    private static $_CMD_RETURN_NONE = array("jcancle", "ping", "quit");
    private static $_CMD_RETURN_INT = array("set", "del", "setx", "expire", "incr", "decr", "exists", "getbit", "setbit",
                                "multi_set", "multi_del",
                                "hset", "hdel", "hsize", "hincr", "hdecr", "hexists", "hclear", "multi_hset", "multi_hdel",
                                "zget", "zset", "zdel", "zsize", "zincr", "zdecr", "zcount", "zclear", 
                                "zremrangebyrank", "zexists", "multi_zset", "multi_zdel",
                                "vset", "vdel", "vsize", "vincr", "vdecr", "vcount", "vclear", 
                                "vremrangebyrank", "vexists", "multi_vset", "multi_vdel",
                                "qsize", "qpush", "qpush_front", "qpush_back", "qclear",
                                "jdelay", "jcancel", "jllen", "jlpush", "jlsetd", "jldeld", "jldel_all");
    private static $_CMD_RETURN_BIN = array("get", "getset", "hget", "jlpop", "jlgetd");
    private static $_CMD_RETURN_LIST = array("keys", "rkeys",
                                "hkeys", "hrkeys", "hlist", "hrlist",
                                "zkeys", "zlist", "zrlist",
                                "vkeys", "vlist", "vrlist",
                                "qpop", "qpop_front", "qpop_back", "qslice", "qrange",
                                "jllist");
    private static $_CMD_RETURN_MAP = array("scan", "rscan", "multi_get",
                                "hscan", "hrscan", "hgetall", "multi_hget",
                                "jlslice");
    private static $_CMD_RETURN_MAP_INT = array("zscan", "zrscan", "zrange", "zrrange", "multi_zget");
    private static $_CMD_RETURN_TABLE = array("mhmget");
    private static $_CMD_RETURN_VSET = array("vget");
    private static $_CMD_RETURN_MAP_VSET = array("vscan", "vrscan", "vrange", "vrrange", "multi_vget");

    function __construct($cmd, $params, $res, $nkey = 0, $nfield = 0) {
        $this->cmd = $cmd;
        $this->params = $params;
        $this->res = $res;
        $this->nkey = $nkey;
        $this->nfield = $nfield;
        $this->val = NULL;
        $this->val_type = "list";
        $this->is_ok = FALSE;
        $this->is_not_found = FALSE;
        $this->is_buffer = FALSE;
        $this->is_error = TRUE;
        $this->error_msg = "";
        $this->parse_response();
    }

    function __toString() {
        //$str = "request: " + $this->write_buf + "\nresponse: " + $this->read_buf;
        //return $str;
    }

    protected function parse_response() {
        if ($this->res == NULL or count($this->res) == 0) {
            $this->is_ok = FALSE;
            $this->is_not_found = FALSE;
            $this->is_buffer = FALSE;
            $this->is_error = TRUE;
        } else {
            if ($this->res[0] == self::_BANYAN_RESPONSE_OK) {
                $this->is_ok = TRUE;
                $this->is_not_found = FALSE;
                $this->is_buffer = FALSE;
                $this->is_error = FALSE;
                $this->parse_ok_response();
            } else if ($this->res[0] == self::_BANYAN_RESPONSE_NOT_FOUND) { 
                $this->is_ok = FALSE;
                $this->is_not_found = TRUE;
                $this->is_buffer = FALSE;
                $this->is_error = FALSE;
            } else if ($this->res[0] == self::_BANYAN_RESPONSE_BUFFER) {
                $this->is_ok = FALSE;
                $this->is_not_found = FALSE;
                $this->is_buffer = TRUE;
                $this->is_error = FALSE;
            } else {
                $this->is_ok = FALSE;
                $this->is_not_found = FALSE;
                $this->is_buffer = FALSE;
                $this->is_error = TRUE;
                if (count($this->res) > 1) {
                    $this->error_msg = $this->res[1];
                }
            }
        }
    }

    protected function parse_ok_response() {
        if (in_array($this->cmd, self::$_CMD_RETURN_NONE)) {
            $this->val = NULL;
            $this->val_type = "None";
        } else if (in_array($this->cmd, self::$_CMD_RETURN_BIN)) {
            $this->val = $this->res[1];
            $this->val_type = "bin";
        } else if (in_array($this->cmd, self::$_CMD_RETURN_INT)) {
            $this->val = intval($this->res[1]);
            $this->val_type = "int";
        } else if (in_array($this->cmd, self::$_CMD_RETURN_LIST)) {
            $this->val = array_slice($this->res, 1);
            $this->val_type = "list";
        } else if (in_array($this->cmd, self::$_CMD_RETURN_VSET)) {
            $this->val = new VSet(0, ""); 
            $n = count($this->res);
            if ($n != 3) {
                $this->is_ok = FALSE;    
                $this->error = TRUE;
                $this->error_msg = "wrong number of vset";
            } else {
                $val = new VSet(intval($this->res[1]), $this->res[2]);
                $this->val = $val;
                $this->val_type = "vset";
            }
        } else if (in_array($this->cmd, self::$_CMD_RETURN_MAP)) {
            $this->val = array();
            $n = count($this->res);
            if ($n % 2 == 0) {
                $this->is_ok = FALSE;    
                $this->error = TRUE;
                $this->error_msg = "wrong number of res items for map";
            } else {
                for ($i = 1; $i < $n; $i += 2) {
                    $this->val[$this->res[$i]] = $this->res[$i+1];
                }
            }
            $this->val_type = "map";
        } else if (in_array($this->cmd, self::$_CMD_RETURN_MAP_INT)) {
            $this->val = array();
            $n = count($this->res);
            for ($i = 1; $i + 1 < $n; $i += 2) {
                $this->val[$this->res[$i]] = intval($this->res[$i+1]);
            }
            $this->val_type = "map_int";
        } else if (in_array($this->cmd, self::$_CMD_RETURN_TABLE)) {        
            $this->val = array();
            $n = count($this->res);
            for ($i = 1; $i + 2 < $n; $i += 3) {
                $key = $this->res[$i];
                $field = $this->res[$i + 1];
                $val = $this->res[$i + 2];
                $items = array();
                if (array_key_exists($key, $this->val)) {
                    $items = $this->val[$key];
                    //print_r($items);
                }
                $items[$field] = $val;
                $this->val[$key] = $items;
            }
            $this->val_type = "table";
        } else if (in_array($this->cmd, self::$_CMD_RETURN_MAP_VSET)) {        
            $this->val = array();
            $n = count($this->res);
            for ($i = 1; $i + 2 < $n; $i += 3) {
                $key = $this->res[$i];
                $vset = new VSet(intval($this->res[$i + 1]), $this->res[$i + 2]);
                $this->val[$key] = $vset;
            }
            $this->val_type = "map_vset";
        } else {
            $this->val = array_slice($this->res, 1);
            $this->val_type = "listd";
        }
    }

    function ok() {
        return $this->is_ok;
    }

    function not_found() {
        return $this->is_not_found;
    }

    function buffer() {
        return $this->is_buffer;
    }

    function error() {
        return $this->is_error;
    }

    function val() {
        return $this->val;
    }
}