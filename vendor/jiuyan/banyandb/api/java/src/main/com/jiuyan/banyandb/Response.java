package com.jiuyan.banyandb;

import java.util.List;
import java.util.Set;
import java.util.HashSet;
import java.util.Map;
import java.util.LinkedHashMap;

public class Response {
    private final static String BANYAN_RESPONSE_OK = "ok";
    private final static String BANYAN_RESPONSE_NOT_FOUND = "not_found";
    private final static String BANYAN_RESPONSE_CLIENT_ERROR = "client_error";
    private final static String BANYAN_RESPONSE_BUFFER = "buffer";
    private final static String BANYAN_RESPONSE_ERROR = "error";

    private final static Set<String> CMD_RETURN_NONE = new HashSet<String>();
    static {
            CMD_RETURN_NONE.add("jcancel");
    }

    private final static Set<String> CMD_RETURN_INT = new HashSet<String>();
    static {
        CMD_RETURN_INT.add("set"); 
        CMD_RETURN_INT.add("del"); 
        CMD_RETURN_INT.add("setx"); 
        CMD_RETURN_INT.add("expire"); 
        CMD_RETURN_INT.add("incr"); 
        CMD_RETURN_INT.add("decr");
        CMD_RETURN_INT.add("exists"); 
        //CMD_RETURN_INT.add("getbit"); 
        //CMD_RETURN_INT.add("setbit"); 
        CMD_RETURN_INT.add("jdelay");
        CMD_RETURN_INT.add("strlen"); 
        CMD_RETURN_INT.add("setnx");
        CMD_RETURN_INT.add("multi_set");
        CMD_RETURN_INT.add("multi_del");
        CMD_RETURN_INT.add("hset"); 
        CMD_RETURN_INT.add("hdel"); 
        CMD_RETURN_INT.add("hdel_if_eq"); 
        CMD_RETURN_INT.add("hsize"); 
        CMD_RETURN_INT.add("hincr"); 
        CMD_RETURN_INT.add("hdecr"); 
        CMD_RETURN_INT.add("hclear"); 
        CMD_RETURN_INT.add("hexists"); 
        CMD_RETURN_INT.add("multi_hset"); 
        CMD_RETURN_INT.add("multi_hdel");
        CMD_RETURN_INT.add("zset"); 
        CMD_RETURN_INT.add("zget"); 
        CMD_RETURN_INT.add("zdel"); 
        CMD_RETURN_INT.add("zsize"); 
        CMD_RETURN_INT.add("zincr"); 
        CMD_RETURN_INT.add("zdecr"); 
        CMD_RETURN_INT.add("zcount"); 
        CMD_RETURN_INT.add("zclear"); 
        CMD_RETURN_INT.add("zremrangebyrank"); 
        CMD_RETURN_INT.add("zexists"); 
        CMD_RETURN_INT.add("zsum"); 
        CMD_RETURN_INT.add("zrank"); 
        CMD_RETURN_INT.add("zrrank"); 
        CMD_RETURN_INT.add("zremrangebyscore"); 
        CMD_RETURN_INT.add("multi_zset"); 
        CMD_RETURN_INT.add("multi_zdel");
        CMD_RETURN_INT.add("vset"); 
        CMD_RETURN_INT.add("vdel"); 
        CMD_RETURN_INT.add("vsize"); 
        CMD_RETURN_INT.add("vincr"); 
        CMD_RETURN_INT.add("vdecr"); 
        CMD_RETURN_INT.add("vcount"); 
        CMD_RETURN_INT.add("vclear"); 
        CMD_RETURN_INT.add("vremrangebyrank"); 
        CMD_RETURN_INT.add("vexists"); 
        CMD_RETURN_INT.add("vsum"); 
        CMD_RETURN_INT.add("vrank"); 
        CMD_RETURN_INT.add("vrrank"); 
        CMD_RETURN_INT.add("vremrangebyscore"); 
        CMD_RETURN_INT.add("multi_vset"); 
        CMD_RETURN_INT.add("multi_vdel");
        CMD_RETURN_INT.add("qsize"); 
        CMD_RETURN_INT.add("qpush"); 
        CMD_RETURN_INT.add("qpush_back"); 
        CMD_RETURN_INT.add("qpush_front"); 
        CMD_RETURN_INT.add("qclear");
        CMD_RETURN_INT.add("jllen"); 
        CMD_RETURN_INT.add("jlpush"); 
        CMD_RETURN_INT.add("jlsetd"); 
        CMD_RETURN_INT.add("jldeld"); 
        CMD_RETURN_INT.add("jldel_all");
    }
    private final static Set<String> CMD_RETURN_DOUBLE = new HashSet<String>();
    static {
        CMD_RETURN_DOUBLE.add("zavg"); 
        CMD_RETURN_DOUBLE.add("vavg");
    }

    private final static Set<String> CMD_RETURN_BIN = new HashSet<String>();
    static {
        CMD_RETURN_BIN.add("get"); 
        CMD_RETURN_BIN.add("getset"); 
        //CMD_RETURN_BIN.add("substr"); 
        CMD_RETURN_BIN.add("hget"); 
        CMD_RETURN_BIN.add("hset_if_eq"); 
        CMD_RETURN_BIN.add("qfornt"); 
        CMD_RETURN_BIN.add("qback");
        CMD_RETURN_BIN.add("jget"); 
        CMD_RETURN_BIN.add("jlpop"); 
        CMD_RETURN_BIN.add("jlgetd");
    }

    private final static Set<String> CMD_RETURN_LIST = new HashSet<String>();
    static {
        CMD_RETURN_LIST.add("keys"); 
        CMD_RETURN_LIST.add("rkeys");
        CMD_RETURN_LIST.add("hkeys"); 
        CMD_RETURN_LIST.add("hrkeys"); 
        CMD_RETURN_LIST.add("hlist"); 
        CMD_RETURN_LIST.add("hrlist"); 
        CMD_RETURN_LIST.add("zlist"); 
        CMD_RETURN_LIST.add("zrlist"); 
        CMD_RETURN_LIST.add("zkeys");
        CMD_RETURN_LIST.add("vlist"); 
        CMD_RETURN_LIST.add("vrlist"); 
        CMD_RETURN_LIST.add("vkeys"); 
        CMD_RETURN_LIST.add("qpop"); 
        CMD_RETURN_LIST.add("qpop_front"); 
        CMD_RETURN_LIST.add("qslice"); 
        CMD_RETURN_LIST.add("qrange");
        CMD_RETURN_LIST.add("jllist"); 
        CMD_RETURN_LIST.add("qpop_back");
    }

    private final static Set<String> CMD_RETURN_MAP = new HashSet<String>();
    static {
        CMD_RETURN_MAP.add("scan");
        CMD_RETURN_MAP.add("rscan"); 
        CMD_RETURN_MAP.add("multi_get"); 
        CMD_RETURN_MAP.add("hgetall");
        CMD_RETURN_MAP.add("hscan");
        CMD_RETURN_MAP.add("hrscan");
        CMD_RETURN_MAP.add("multi_hget"); 
    }

    private final static Set<String> CMD_RETURN_MAP_STRINGLONG = new HashSet<String>();
    static {
        CMD_RETURN_MAP_STRINGLONG.add("zrange");
        CMD_RETURN_MAP_STRINGLONG.add("zrrange");
        CMD_RETURN_MAP_STRINGLONG.add("zscan");
        CMD_RETURN_MAP_STRINGLONG.add("zrscan");
        CMD_RETURN_MAP_STRINGLONG.add("multi_zget");
    }

    private final static Set<String> CMD_RETURN_MAP_LONGSTRING = new HashSet<String>();
    static {
        CMD_RETURN_MAP_LONGSTRING.add("jlslice");
    }

    private final static Set<String> CMD_RETURN_TABLE = new HashSet<String>();
    static {
            CMD_RETURN_TABLE.add("mhmget");
    }

    private final static Set<String> CMD_RETURN_VSET = new HashSet<String>();
    static {
        CMD_RETURN_VSET.add("vget"); 
    }
    private final static Set<String> CMD_RETURN_MAP_VSET = new HashSet<String>();
    static {
        CMD_RETURN_MAP_VSET.add("vrange"); 
        CMD_RETURN_MAP_VSET.add("vrrange"); 
        CMD_RETURN_MAP_VSET.add("vscan"); 
        CMD_RETURN_MAP_VSET.add("vrscan"); 
        CMD_RETURN_MAP_VSET.add("multi_vget"); 
    }

    private String cmd;
    private String status;
    private List<String> reps;
    private List<String> params;
    private boolean is_ok = false;
    private boolean is_not_found = false;
    private boolean is_client_error = false;
    private boolean is_buffer = false;
    private boolean is_error = false;
    private Object val = null;
    // prival Oject val = new Object();

    public Response(String cmd, List<String> params, List<String> reps) throws BanyanDBException {
        this.cmd = cmd;
        this.params = params;
        this.reps = reps;
        this.parasResponse();
    }

    public Object exception() throws BanyanDBException {
        StringBuffer paramString = new StringBuffer();
        for (String s : this.params) {
            paramString.append(s + ",");
        }
        StringBuffer repString = new StringBuffer();
        for (String s : this.reps) {
            repString.append(s + ",");
        }
        throw new BanyanDBException("request: " + paramString + "\nresponse: " + repString);
    }

    private void parasResponse() throws BanyanDBException {
        if (this.reps.size() < 1) {
            this.exception();
        }
        this.status = reps.get(0);
        if (this.status.equals(BANYAN_RESPONSE_OK)) {
            this.is_ok = true;
            this.parseOKResponse();
        } else if (this.status.equals(BANYAN_RESPONSE_NOT_FOUND)) {
            this.is_not_found = true;
        } else if (this.status.equals(BANYAN_RESPONSE_CLIENT_ERROR)) {
            this.is_client_error = true;
        } else if (this.status.equals(BANYAN_RESPONSE_BUFFER)) {
            this.is_buffer = true;
        } else if (this.status.equals(BANYAN_RESPONSE_ERROR)){
            this.is_error = true;
            if (this.cmd == "hset_if_eq") {
                if (this.reps.size() > 1) {
                    this.val = this.reps.get(1);
                    return;
                }
            }
        } else {
            this.is_error = true;
        }

        if (!(this.is_ok || this.is_not_found || this.is_buffer)) {
            this.exception();
        }
    }

    private void parseOKResponse() {
       if (CMD_RETURN_NONE.contains(this.cmd)) {
       } else if (CMD_RETURN_INT.contains(this.cmd)) {
          this.val = Long.parseLong(this.reps.get(1));
       } else if (CMD_RETURN_DOUBLE.contains(this.cmd)) {
          this.val = Double.parseDouble(this.reps.get(1));
       } else if (CMD_RETURN_BIN.contains(this.cmd)) {
           this.val = this.reps.get(1);
       } else if (CMD_RETURN_LIST.contains(this.cmd)) {
            /*List<String> val = new ArrayList<String>();
            for (int i = 1; i < this.reps.size(); i++) {
                val.add(this.reps.get(i));
            }*/
           int size = this.reps.size();
           this.val = this.reps.subList(1, size);
       } else if (CMD_RETURN_MAP.contains(this.cmd)) {
            Map<String, String> map = new LinkedHashMap<String, String>();
            for (int i = 1; i + 1 < this.reps.size(); i += 2) {
                String k = this.reps.get(i);
                String v = this.reps.get(i + 1);
                map.put(k, v);
            }
            this.val = map;
       } else if (CMD_RETURN_MAP_STRINGLONG.contains(this.cmd)) {
            Map<String, Long> map = new LinkedHashMap<String, Long>();
            for (int i = 1; i + 1 < this.reps.size(); i += 2) {
                String m = this.reps.get(i);
                long s = Long.parseLong(this.reps.get(i + 1));
                map.put(m, s);
            }
            this.val = map;
       } else if (CMD_RETURN_MAP_LONGSTRING.contains(this.cmd)) {
            Map<Long, String> map = new LinkedHashMap<Long, String>();
            for (int i = 1; i + 1 < this.reps.size(); i += 2) {
                long s = Long.parseLong(this.reps.get(i));
                String m = this.reps.get(i + 1);
                map.put(s, m);
            }
            this.val = map;
       } else if (CMD_RETURN_TABLE.contains(this.cmd)) {
            Map<String, Map<String, String>> table = new LinkedHashMap<String, Map<String, String>>();
            for (int i = 1; i + 2 < this.reps.size(); i += 3) {
                String k = this.reps.get(i);
                String f = this.reps.get(i + 1);
                String v = this.reps.get(i + 2);
                if (table.containsKey(k)) {
                    Map<String, String> fv = table.get(k);
                    fv.put(f, v);
                } else {
                    Map<String, String> fv = new LinkedHashMap<String, String>();
                    fv.put(f, v);
                    table.put(k, fv);
                }
            }
            this.val = table;
       } else if (CMD_RETURN_VSET.contains(this.cmd)) {
           if (this.reps.size() > 2) {
                VSetValue v = new VSetValue(Long.parseLong(this.reps.get(1)), this.reps.get(2));
                this.val = v;
           }
       } else if (CMD_RETURN_MAP_VSET.contains(this.cmd)) {
            Map<String, VSetValue> map = new LinkedHashMap<String, VSetValue>();
            for (int i = 1; i + 2 < this.reps.size(); i += 3) {
                String k = this.reps.get(i);
                VSetValue v = new VSetValue(Long.parseLong(this.reps.get(i + 1)), this.reps.get(i + 2));
                map.put(k, v);
            }
            this.val = map;
       } else {
           int size = this.reps.size();
           this.val = this.reps.subList(1, size);
       }
    }

    public boolean ok() {
        return this.is_ok;
    }

    public boolean not_found() {
        return this.is_not_found;
    }

    public boolean buffer() {
        return this.is_buffer;
    }

    public boolean client_error() {
        return this.is_client_error;
    }

    public boolean error() {
        return this.is_error;
    }

    public Object val() {
        return this.val;
    }
}
