package com.jiuyan.banyandb;

import java.util.List;
import java.util.ArrayList;
import java.util.Map;
import java.util.UUID;
import java.io.IOException;

public class BanyanDBClient {
    public ClusterLink clusterlink;
    private boolean isDebug = false;
    private long start_ms = 0;
    private long end_ms = 0;
    private String readOption = "";
    private String nginxRequestId = "";
    private String ns;
    private String table;
    private String delay_ns = "banyan";
    private String delay_table = "delay";

    public BanyanDBClient(ClusterLink clusterlink, String ns, String table) {
        this.clusterlink = clusterlink;
        this.ns = ns;
        this.table = table;
    }

    public String getNameSpace() {
        return this.ns;
    }

    public String getTable() {
        return this.table;
    }

    public void setDebug(boolean debug) {
        this.isDebug = debug;
    }

    /* opt = master|slave */
    public void setReadOption(String opt) {
        this.readOption = opt;
    }

    public void setNginxRequestId(String id) {
        this.nginxRequestId = id;
    }

    /* ms */
    public long getRequestLatency() {
        return end_ms - start_ms;
    }

    private String getOptions() {
        String uuid = "java_" + UUID.randomUUID().toString();
        String options = "ns:" + this.ns + ",tab:" + this.table + ",proto:by,rid:" + uuid;
        if (this.readOption.length() > 0) {
            options += (",read:" + readOption);
        }
        if (this.nginxRequestId.length() > 0) {
            options += (",ngx:" + nginxRequestId);
        }
        return options;
    }

    private boolean is_ok(Response res) {
        if (res.ok() || res.buffer()) {
            return true;
        }
        return false;
    } 

    private long is_ok_long(Response res) {
        if (res.ok()) {
            return (Long)res.val();
        }
        // buffer
        return -1;
    } 

    /*===kv===*/
    public String get(String key) throws Exception {
        Response res = this.request("get", key);
        if (res.not_found()) {
            return null;
        }
        return res.val().toString();
    }

    public boolean set(String key, String val) throws Exception {
        Response res = this.request("set", key, val);
        return this.is_ok(res);
    }

    public long strlen(String key) throws Exception {
        return (Long)this.request("strlen", key).val();
    }

    /*public String substr(String key) throws Exception {
        return (String)this.request("substr", key).val();
    }*/

    public String getset(String key, String val) throws Exception {
        Response res = this.request("getset", key, val);
        if (res.not_found()) {
            return null;
        } else {
            return res.val().toString();
        }
    }

    public boolean setx(String key, String val, int ttl) throws Exception {
        Response res = this.request("setx", key, val, String.valueOf(ttl));
        return this.is_ok(res);
    }

    public boolean expire(String key, int ttl) throws Exception {
        Response res = this.request("expire", key, String.valueOf(ttl));
        return this.is_ok(res);
    }

    public boolean del(String key) throws Exception {
        Response res = this.request("del", key);
        return this.is_ok(res);
    }

    public long incr(String key, long incr) throws Exception {
        return (Long)this.request("incr", key, String.valueOf(incr)).val();
    }

    public long decr(String key, long incr) throws Exception {
        return (Long)this.request("decr", key, String.valueOf(incr)).val();
    }

    public boolean exists(String key) throws Exception {
        long flags = (Long)this.request("exists", key).val();
        return flags == 1 ? true : false;
    }
    /*
    public boolean getbit(String key, long offset) throws Exception {
        int falgs = (int)this.request("getbit", key).val();
        return flags ? true : false;
    }

    public boolean setbit(String key, long offset) throws Exception {
        int falgs = (int)this.request("setbit", key).val();
        return flags ? true : false;
    }*/

    public Map<String, String> scan(String start, String end, int limit) throws Exception {
        Object obj = this.request("scan", start, end, String.valueOf(limit)).val();
        Map<String, String> map = uncheckedCast(obj);
        return map;
    }

    public Map<String,String> rscan(String start, String end, int limit) throws Exception {
        Object obj = this.request("rscan", start, end, String.valueOf(limit)).val();
        Map<String, String> map = uncheckedCast(obj);
        return map;
    }

    public List<String> keys(String key, String end, int limit) throws Exception {
        Object obj = this.request("keys", key, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public List<String> rkeys(String key, String end, int limit) throws Exception {
        Object obj = this.request("rkeys", key, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public Map<String, String> multi_get(List<String> keys) throws Exception {
        Object obj = this.request("multi_get", keys).val();
        Map<String, String> map = uncheckedCast(obj);
        return map;
    }

    public boolean multi_set(Map<String, Object> kvs) throws Exception {
        List<String> args = new ArrayList<String>(2 * kvs.size() + 1);
        for (Map.Entry<String, Object> kv : kvs.entrySet()) {
            args.add(kv.getKey());
            args.add(String.valueOf(kv.getValue()));
        }
        Response res = this.request("multi_set", args);
        return this.is_ok(res);
    }

    public boolean mulit_del(List<String> keys) throws Exception {
        Response res = this.request("multi_del", keys);
        return this.is_ok(res);
    }

    /* jdelay */
    public long jdelay(long ttl, String... args) throws Exception {
        List<String> params = new ArrayList<String>(8);
        String uuid = "java_" + UUID.randomUUID().toString();
        String options1 = "ns:" + this.ns + ",tab:" + this.table + ",proto:by,rid:" + uuid;
        if (this.nginxRequestId.length() > 0) {
            options1 += (",ngx:" + nginxRequestId);
        }
        params.add(options1);
        for (String s : args) {
            params.add(s);
        }
        String statements = this.serizlize(params);
        String options2 = "ns:" + this.delay_ns + ",tab:" + this.delay_table + ",proto:by,rid:" + uuid;
        if (this.nginxRequestId.length() > 0) {
            options2 += (",ngx:" + nginxRequestId);
        }

        params.clear();
        params.add(options2);
        params.add("jdelay");
        params.add(statements);
        params.add(String.valueOf(ttl));
        return (Long)this.request2("jdelay", params).val();
    }

    public boolean jcancel(long seq) throws Exception {
        List<String> params = new ArrayList<String>(4);
        String uuid = "java_" + UUID.randomUUID().toString();
        String options = "ns:" + this.delay_ns + ",tab:" + this.delay_table + ",proto:by,rid:" + uuid;
        if (this.nginxRequestId.length() > 0) {
            options += (",ngx:" + nginxRequestId);
        }
        params.add(options);
        params.add("jcancel");
        params.add(String.valueOf(seq));
        return this.request2("jcancel", params).ok();
    }

    /* ===hash=== */
    public long hsize(String key) throws Exception {
        return (Long)this.request("hsize", key).val();
    }

    public String hget(String key, String field) throws Exception {
        Response res = this.request("hget", key, field);
        if (res.not_found()) {
            return null;
        }
        return res.val().toString();
    }

    public boolean hset(String key, String field, String val) throws Exception {
        Response res = this.request("hset", key, field, val);
        return this.is_ok(res);
    }

    public String hset_if_eq(String key, String field, String val, String oldval) throws Exception {
        Response res = this.request("hset_if_eq", key, field, val, oldval);
        if (res.error()) {
            return res.val().toString();
        } else if (res.not_found()) {
            return new String("");
        }
        return null;
    }

    public boolean hdel(String key, String field) throws Exception {
        Response res = this.request("hdel", key, field);
        return this.is_ok(res);
    }

    public boolean hdel_if_eq(String key, String field, String oldval) throws Exception {
        Response res = this.request("hdel_if_eq", key, field, oldval);
        long flags = (Long)res.val();
        return flags == 1 ? true : false;
    }

    public long hincr(String key, String field, long incr) throws Exception {
        return (Long)this.request("hincr", key, field, String.valueOf(incr)).val();
    }

    public long hdecr(String key, String field, long incr) throws Exception {
        return (Long)this.request("hdecr", key, field, String.valueOf(incr)).val();
    }

    public Map<String, String> hgetall(String key) throws Exception {
        Object obj = this.request("hgetall", key).val();
        Map<String, String> map = uncheckedCast(obj);
        return map;
    }

    public boolean hclear(String key) throws Exception {
        Response res = this.request("hclear", key);
        return this.is_ok(res);
        //return (Long)this.request("hclear", key).val();
    }

    public boolean hexists(String key, String field) throws Exception {
        long flags = (Long)this.request("hexists", key, field).val();
        return flags == 1 ? true : false;
    }

    public Map<String, String> hscan(String key, String start, String end, int limit) throws Exception {
        Object obj = this.request("hscan", key, start, end, String.valueOf(limit)).val();
        Map<String, String> map = uncheckedCast(obj);
        return map;
    }

    public Map<String, String> hrscan(String key, String start, String end, int limit) throws Exception {
        Object obj = this.request("hrscan", key, start, end, String.valueOf(limit)).val();
        Map<String, String> map = uncheckedCast(obj);
        return map;
    }

    public List<String> hkeys(String key, String start, String end, int limit) throws Exception {
        Object obj = this.request("hkeys", key, start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public List<String> hrkeys(String key, String start, String end, int limit) throws Exception {
        Object obj = this.request("hrkeys", key, start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public List<String> hlist(String start, String end, int limit) throws Exception {
        Object obj = this.request("hlist", start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public List<String> hrlist(String start, String end, int limit) throws Exception {
        Object obj = this.request("hrlist", start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public Map<String, String> multi_hget(String key, List<String> fields) throws Exception {
        List<String> args = new ArrayList<String>(fields.size() + 1);
        args.add(key);
        args.addAll(fields);
        Object obj = this.request("multi_hget", args).val();
        Map<String, String> map = uncheckedCast(obj);
        return map;
    }

    public boolean multi_hset(String key, Map<String, Object> kvs) throws Exception {
        List<String> args = new ArrayList<String>(2 * kvs.size() + 1);
        args.add(key);
        for (Map.Entry<String, Object> kv : kvs.entrySet()) {
            args.add(kv.getKey());
            args.add(String.valueOf(kv.getValue()));
        }
        Response res = this.request("multi_hset", args);
        return this.is_ok(res);
    }

    public boolean multi_hdel(String key, List<String> fields) throws Exception {
        List<String> args = new ArrayList<String>(fields.size() + 1);
        args.add(key);
        args.addAll(fields);
        Response res = this.request("multi_hdel", args);
        return this.is_ok(res);
    }

    public Map<String, Map<String, String>> mhmget(List<String> keys, List<String> fields) throws Exception {
        List<String> args = new ArrayList<String>(keys.size() + fields.size() + 2);
        String options = this.getOptions();
        options += (",nkey:" + String.valueOf(keys.size()) + ",nfield:" + String.valueOf(fields.size()));
        args.add(options);
        args.add("mhmget");
        args.addAll(keys);
        args.addAll(fields);
        Object obj = this.request2("mhmget", args).val();
        Map<String, Map<String, String>> table = uncheckedCast(obj);
        return table;
    }

    /* ===zset=== */
    public long zsize(String key) throws Exception {
        return (Long)this.request("zsize", key).val();
    }

    public long zget(String key, String member) throws Exception {
        Response res = this.request("zget", key, member);
        if (res.not_found()) {
            return -1;
        }
        return (Long)res.val();
    }

    public boolean zset(String key, String member, long score) throws Exception {
        Response res = this.request("zset", key, member, String.valueOf(score));
        return this.is_ok(res);
    }

    public boolean zdel(String key, String member) throws Exception {
        Response res = this.request("zdel", key, member);
        return this.is_ok(res);
    }

    public long zincr(String key, String member, long incr) throws Exception {
        return (Long)this.request("zincr", key, member, String.valueOf(incr)).val();
    }

    public long zdecr(String key, String member, long incr) throws Exception {
        return (Long)this.request("zdecr", key, member, String.valueOf(incr)).val();
    }

    public boolean zclear(String key) throws Exception {
        Response res = this.request("zclear", key);
        return this.is_ok(res);
    }

    public boolean zexists(String key, String member) throws Exception {
        long flags = (Long)this.request("zexists", key, member).val();
        return flags == 1 ? true : false;
    }

    public long zcount(String key, long start, long end) throws Exception {
        return (Long)this.request("zcount", key, String.valueOf(start), String.valueOf(end)).val();
    }

    public long zremrangebyrank(String key, long start, long end) throws Exception {
        return (Long)this.request("zremrangebyrank", key, String.valueOf(start), String.valueOf(end)).val();
    }

    public long zremrangebyscore(String key, long start, long end) throws Exception {
        return (Long)this.request("zremrangebyscore", key, String.valueOf(start), String.valueOf(end)).val();
    }

    public long zrank(String key, String member) throws Exception {
        Response res = this.request("zrank", key, member); 
        if (res.not_found()) {
            return -1;
        }
        return (Long)res.val();
    }

    public long zrrank(String key, String member) throws Exception {
        Response res = this.request("zrrank", key, member); 
        if (res.not_found()) {
            return -1;
        }
        return (Long)res.val();
    }

    public long zavg(String key) throws Exception { 
        return (Long)this.request("zavg", key).val(); 
    }

    public long zsum(String key) throws Exception {
        return (Long)this.request("zsum", key).val(); 
    }

    public List<String> zkeys(String key, String startMember, long start, long end, int limit) throws Exception {
        Object obj = this.request("zkeys", key, startMember, String.valueOf(start), String.valueOf(end), String.valueOf(limit)).val(); 
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public Map<String, Long> zrange(String key, long offset, int limit) throws Exception {
        Object obj = this.request("zrange", key, String.valueOf(offset), String.valueOf(limit)).val();
        Map<String, Long> map = uncheckedCast(obj);
        return map;
    }

    public Map<String, Long> zrrange(String key, long offset, int limit) throws Exception {
        Object obj = this.request("zrrange", key, String.valueOf(offset), String.valueOf(limit)).val();
        Map<String, Long> map = uncheckedCast(obj);
        return map;
    }

    public Map<String, Long> zscan(String key, String startMember, long start, long end, int limit) throws Exception {
        Object obj = this.request("zscan", key, startMember, String.valueOf(start), String.valueOf(end), String.valueOf(limit)).val();
        Map<String, Long> map = uncheckedCast(obj);
        return map;
    }

    public Map<String, Long> zrscan(String key, String startMember, long start, long end, int limit) throws Exception {
        Object obj = this.request("zrscan", key, startMember, String.valueOf(start), String.valueOf(end), String.valueOf(limit)).val();
        Map<String, Long> map = uncheckedCast(obj);
        return map;
    }

    public List<String> zlist(String start, String end, int limit) throws Exception {
        Object obj = this.request("zlist", start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public List<String> zrlist(String start, String end, int limit) throws Exception {
        Object obj = this.request("zrlist", start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public Map<String, Long> multi_zget(String key, List<String> members) throws Exception {
        List<String> args = new ArrayList<String>(members.size() + 1);
        args.add(key);
        args.addAll(members);
        Object obj = this.request("multi_zget", args).val();
        Map<String, Long> map = uncheckedCast(obj);
        return map;
    }

    public boolean multi_zset(String key, Map<String, Object> kvs) throws Exception {
        List<String> args = new ArrayList<String>(2 * kvs.size() + 1);
        args.add(key);
        for (Map.Entry<String, Object> kv : kvs.entrySet()) {
            args.add(kv.getKey());
            args.add(String.valueOf(kv.getValue()));
        }
        Response res = this.request("multi_zset", args);
        return this.is_ok(res);
    }

    public boolean multi_zdel(String key, List<String> members) throws Exception {
        List<String> args = new ArrayList<String>(members.size() + 1);
        args.add(key);
        args.addAll(members);
        Response res = this.request("multi_zdel", args);
        return this.is_ok(res);
    }

    /* ===vset=== */
    public long vsize(String key) throws Exception {
        return (Long)this.request("vsize", key).val();
    }

    public VSetValue vget(String key, String member) throws Exception {
        Response res = this.request("vget", key, member);
        if (res.not_found()) {
            return null;
        }
        VSetValue val = uncheckedCast(res.val());
        return val;
    }

    public boolean vset(String key, String member, long score, String value) throws Exception {
        Response res = this.request("vset", key, member, String.valueOf(score), value);
        return this.is_ok(res);
    }

    public boolean vdel(String key, String member) throws Exception {
        Response res = this.request("vdel", key, member);
        return this.is_ok(res);
    }

    public long vincr(String key, String member, long incr) throws Exception {
        return (Long)this.request("vincr", key, member, String.valueOf(incr)).val();
    }

    public long vdecr(String key, String member, long incr) throws Exception {
        return (Long)this.request("vdecr", key, member, String.valueOf(incr)).val();
    }

    public boolean vclear(String key) throws Exception {
        Response res = this.request("vclear", key);
        return this.is_ok(res);
    }

    public boolean vexists(String key, String member) throws Exception {
        long flags = (Long)this.request("vexists", key, member).val();
        return flags == 1 ? true : false;
    }

    public long vcount(String key, long start, long end) throws Exception {
        return (Long)this.request("vcount", key, String.valueOf(start), String.valueOf(end)).val();
    }

    public long vremrangebyrank(String key, long start, long end) throws Exception {
        return (Long)this.request("vremrangebyrank", key, String.valueOf(start), String.valueOf(end)).val();
    }

    public long vremrangebyscore(String key, long start, long end) throws Exception {
        return (Long)this.request("vremrangebyscore", key, String.valueOf(start), String.valueOf(end)).val();
    }

    public long vrank(String key, String member) throws Exception {
        Response res = this.request("vrank", key, member); 
        if (res.not_found()) {
            return -1;
        }
        return (Long)res.val();
    }

    public long vrrank(String key, String member) throws Exception {
        Response res = this.request("vrrank", key, member); 
        if (res.not_found()) {
            return 0;
        }
        return (Long)res.val();
    }

    public long vavg(String key) throws Exception { 
        return (Long)this.request("vavg", key).val(); 
    }

    public long vsum(String key) throws Exception {
        return (Long)this.request("vsum", key).val(); 
    }

    public List<String> vkeys(String key, String startMember, long start, long end, int limit) throws Exception {
        Object obj = this.request("vkeys", key, startMember, String.valueOf(start), String.valueOf(end), String.valueOf(limit)).val(); 
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public Map<String, VSetValue> vrange(String key, long offset, int limit) throws Exception {
        Object obj = this.request("vrange", key, String.valueOf(offset), String.valueOf(limit));
        Map<String, VSetValue> map = uncheckedCast(obj);
        return map;
    }

    public Map<String, VSetValue> vrrange(String key, long offset, int limit) throws Exception {
        Object obj = this.request("vrrange", key, String.valueOf(offset), String.valueOf(limit));
        Map<String, VSetValue> map = uncheckedCast(obj);
        return map;
    }

    public Map<String, VSetValue> vscan(String key, String startMember, long start, long end, int limit) throws Exception {
        Object obj = this.request("vscan", key, startMember, String.valueOf(start), String.valueOf(end), String.valueOf(limit)).val();
        Map<String, VSetValue> map = uncheckedCast(obj);
        return map;
    }

    public Map<String, VSetValue> vrscan(String key, String startMember, long start, long end, int limit) throws Exception {
        Object obj = this.request("vrscan", key, startMember, String.valueOf(start), String.valueOf(end), String.valueOf(limit)).val();
        Map<String, VSetValue> map = uncheckedCast(obj);
        return map;
    }

    public List<String> vlist(String start, String end, int limit) throws Exception {
        Object obj = this.request("vlist", start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public List<String> vrlist(String start, String end, int limit) throws Exception {
        Object obj = this.request("vrlist", start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public Map<String, VSetValue> multi_vget(String key, List<String> members) throws Exception {
        List<String> args = new ArrayList<String>(members.size() + 1);
        args.add(key);
        args.addAll(members);
        Object obj = this.request("multi_vget", args).val();
        Map<String, VSetValue> map = uncheckedCast(obj);
        return map;
    }

    public boolean multi_vset(String key, Map<String, Object> kvs) throws Exception {
        VSetValue tmp = null;
        List<String> args = new ArrayList<String>(3 * kvs.size() + 1);
        args.add(key);
        for (Map.Entry<String, Object> kv : kvs.entrySet()) {
            args.add(kv.getKey());
            tmp = (VSetValue)kv.getValue();
            args.add(String.valueOf(tmp.getScore()));
            args.add(tmp.getValue());
        }
        Response res = this.request("multi_vset", args);
        return this.is_ok(res);
    }

    public boolean multi_vdel(String key, List<String> members) throws Exception {
        List<String> args = new ArrayList<String>(members.size() + 1);
        args.add(key);
        args.addAll(members);
        Response res = this.request("multi_vdel", args);
        return this.is_ok(res);
    }

    /* ===queue=== */
    public long qsize(String key) throws Exception {
        return (Long)this.request("qsize", key).val();
    }

    public String qfront(String key) throws Exception {
        Response res = this.request("qfront", key);
        if (res.not_found()) {
            return null;
        }
        return res.val().toString();
    }

    public String qback(String key) throws Exception {
        Response res = this.request("qback", key);
        if (res.not_found()) {
            return null;
        }
        return res.val().toString();
    }

    public String qpop_front(String key) throws Exception {
        Object obj = this.request("qpop_front", key).val();
        List<String> list = uncheckedCast(obj);
        if (list.size() > 0) {
            return list.get(0);
        }
        return null;
    }

    public List<String> qpop_front(String key, int limit) throws Exception {
        Object obj = this.request("qpop_front", key, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public String qpop_back(String key) throws Exception {
        Object obj = this.request("qpop_back", key).val();
        List<String> list = uncheckedCast(obj);
        if (list.size() > 0) {
            return list.get(0);
        }
        return null;
    }

    public List<String> qpop_back(String key, int limit) throws Exception {
        Object obj = this.request("qpop_back", key, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public long qpush_front(String key, String member) throws Exception {
        Response res = this.request("qpush_front", key, member);
        return this.is_ok_long(res);
    }

    public long qpush_front(String key, List<String> members) throws Exception {
        List<String> args = new ArrayList<String>(members.size() + 1);
        args.add(key);
        args.addAll(members);
        Response res = this.request("qpush_front", args);
        return this.is_ok_long(res);
    }

    public long qpush_back(String key, String member) throws Exception {
        Response res = this.request("qpush_back", key, member);
        return this.is_ok_long(res);
    }

    public long qpush_back(String key, List<String> members) throws Exception {
        List<String> args = new ArrayList<String>(members.size() + 1);
        args.add(key);
        args.addAll(members);
        Response res = this.request("qpush_back", args);
        return this.is_ok_long(res);
    }

    public String qget(String key, int index) throws Exception {
        Response res = this.request("qget", key, String.valueOf(index));
        if (res.not_found()) {
            return null;
        }
        return res.val().toString();
    }

    public boolean qset(String key, int index, String member) throws Exception {
        Response res = this.request("qset", key, String.valueOf(index), member);
        return this.is_ok(res);
    }

    public long qtrim_front(String key, int index) throws Exception {
        Response res = this.request("qtrim_front", key, String.valueOf(index));
        return this.is_ok_long(res);
    }

    public long qtrim_back(String key, int index) throws Exception {
        Response res = this.request("qtrim_back", key, String.valueOf(index));
        return this.is_ok_long(res);
    }

    public boolean qclear(String key) throws Exception {
        Response res = this.request("qclear", key);
        return this.is_ok(res);
    }

    public List<String> qlist(String start, String end, int limit) throws Exception {
        Object obj = this.request("qlist", start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public List<String> qrlist(String start, String end, int limit) throws Exception {
        Object obj = this.request("qrlist", start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public List<String> qslice(String key, int start, int end) throws Exception {
        Object obj = this.request("qslice", String.valueOf(end), String.valueOf(end)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public List<String> qrange(String key, int start, int limit) throws Exception {
        Object obj = this.request("qrange", String.valueOf(start), String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    /* ===jlist=== */
    public long jllen(String key) throws Exception {
        return (Long)this.request("jllen", key).val();
    }

    public String jlpop(String key) throws Exception {
        return this.request("jlpop", key).val().toString();
    }

    public boolean jlpush(String key, String item, long ttl) throws Exception {
        return this.request("jlpush", key, item, String.valueOf(ttl)).ok();
    }

    public boolean jlpush(String key, long seq, String item, long ttl) throws Exception {
        return this.request("jlpush", key, String.valueOf(seq), item, String.valueOf(ttl)).ok();
    }

    public Map<Long, String> jlslice(String key, long start, int limit) throws Exception {
        Object obj = this.request("jlslice", key, String.valueOf(start), String.valueOf(limit)).val();
        Map<Long, String> map = uncheckedCast(obj);
        return map;
    }

    public String jlgetd(String key, long seq) throws Exception {
        return this.request("jlgetd", key, String.valueOf(seq)).val().toString();
    }

    public boolean jlsetd(String key, long seq, String item) throws Exception {
        return this.request("jlsetd", key, String.valueOf(seq), item).ok();
    }

    public boolean jldeld(String key, long seq) throws Exception {
        return this.request("jldeld", key, String.valueOf(seq)).ok();
    }

    public boolean jldel_all(String key) throws Exception {
        return this.request("jldel_all", key).ok(); 
    }

    public List<String> jllist(String start, String end, int limit) throws Exception {
        Object obj = this.request("jllist", start, end, String.valueOf(limit)).val();
        List<String> list = uncheckedCast(obj);
        return list;
    }

    public Response request(String cmd, String... args) throws Exception {
        ArrayList<String> params = new ArrayList<String>(8);
        params.add(this.getOptions());
        params.add(cmd);
        for (String s : args) {
            params.add(s);
        }

        return this.request2(cmd, params);
    }

    private Response request(String cmd, List<String> args) throws Exception {
        ArrayList<String> params = new ArrayList<String>(16);
        params.add(this.getOptions());
        params.add(cmd);
        params.addAll(args);
        return this.request2(cmd, params);
    }

    private Response request2(String cmd, List<String> params) throws Exception {
        int retry = 0;
        Response res = null;
        List<String> reps = new ArrayList<String>();
        while (true) {
            if (retry >= this.clusterlink.getMaxRetry()) {
                break;
            }
            retry++;
            this.start_ms = System.currentTimeMillis();
            Link link = this.clusterlink.getLink();
            try {
                if (link != null) {
                    String s = this.serizlize(params);
                    res = link.request(cmd, s, params);
                    link.pool.returnLink(link, false);
                    this.end_ms = System.currentTimeMillis();
                    if (this.isDebug) {
                        link.printRequestResponse(this.getRequestLatency());
                    }
                } else {
                    reps.add("error");
                    reps.add("getlink failed");
                    res = new Response(cmd, params, reps);
                }
                return res;
            } catch (IOException e) {
                link.pool.returnLink(link, true);
                e.printStackTrace();
                continue;
            }
        }

        reps.add("error");
        reps.add("retry failed");
        res = new Response(cmd, params, reps);
        return res;
    }

    public String serizlize(List<String> params) {
        Integer len;
        String output = "";
        for (String s : params) {
            len = s.length();
            output += len.toString();
            output += "\n";
            output += s;
            output += "\n";
        }
        output += "\n";
        return output;
    }

    @SuppressWarnings({"unchecked"})
    private static <T> T uncheckedCast(Object obj) {
        return (T) obj;
    }
}
