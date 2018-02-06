import java.util.List;
import java.util.ArrayList;
import java.util.Map;
import java.util.HashMap;
import java.util.LinkedHashMap;
import org.junit.Test;
import org.junit.Test;
import static org.junit.Assert.assertEquals;
import com.jiuyan.banyandb.ClusterLink;
import com.jiuyan.banyandb.BanyanDBClient;
import com.jiuyan.banyandb.VSetValue;

public class BanyanDBTest {
    
    @Test
    public void testKV() {
        try {
            long t = System.currentTimeMillis() / 1000;
            String prefix = "agent_" + String.valueOf(t) + "_";
            ClusterLink clusterlink = new ClusterLink("10.10.105.5:10025", 1024);
            BanyanDBClient cli = new BanyanDBClient(clusterlink, "test", "api_test");
            //cli.setDebug(true);
            //cli.setReadOption("master");
            //cli.setNginxRequestId("nginxxxxx");
            long resLong = 0;
            boolean resBool = false;
            String  resStr = "";

            String key = prefix + "k0";
            String val = "v0";
            resBool = cli.set(key, val);
            assertEquals(resBool, true);
            resStr = cli.get(key);
            assertEquals(resStr, val);
            resLong = cli.strlen(key);
            assertEquals(resLong, 2);
            val = "v1";
            resStr = cli.getset(key, val);
            assertEquals(resStr, "v0");
            resBool = cli.del(key);
            key = prefix + "k1";
            resLong = cli.incr(key, 888);
            assertEquals(resLong, 888);
            resLong = cli.decr(key, 444);
            assertEquals(resLong, 444);
            key = prefix + "k2";
            resBool = cli.exists(key);
            assertEquals(resBool, false);
            resBool = cli.expire(key, 3);
            assertEquals(resBool, true);
            key = prefix + "k3";
            resBool = cli.setx(key, val, 3);
            assertEquals(resBool, true);
            Map<String, Object> params = new LinkedHashMap<String, Object>();
            for (int i = 0; i < 1000; i++) {
                key = String.format("%sk%08d", prefix, i);
                val = String.format("v%08d", i);
                params.put(key, val);
            }
            resBool = cli.multi_set(params);
            assertEquals(resBool, true);
            List<String> keys = new ArrayList<String>();
            String key1 = String.format("%sk%08d", prefix, 1);
            String key2 = String.format("%sk%08d", prefix, 2);
            String val1 = String.format("v%08d", 1);
            String val2 = String.format("v%08d", 2);
            keys.add(key1);
            keys.add(key2);
            Map<String, String> resMap = cli.multi_get(keys);
            assertEquals(resMap.get(key1), val1);
            assertEquals(resMap.get(key2), val2);
            String start = String.format("%sk%08d", prefix, 0);
            String end = String.format("%sk%08d", prefix, 10);
            resMap.clear();
            resMap = cli.scan(start, end, 4);
            assertEquals(resMap.size(), 4);
            assertEquals(resMap.get(key1), val1);
            List<String> resList = cli.keys(start, end, 4);
            assertEquals(resList.size(), 4);
            assertEquals(resList.get(0), key1);
            //resBool = cli.multi_del(params);
            //assertEquals(resBool, true);
        } catch (Exception e) {
            e.printStackTrace();
            assertEquals(false, true);
        }
    }

    @Test
    public void testHASH() {
        try {
            long t = System.currentTimeMillis() / 1000;
            String prefix = "agent_" + String.valueOf(t) + "_";
            ClusterLink clusterlink = new ClusterLink("10.10.105.5:10025", 1024);
            BanyanDBClient cli = new BanyanDBClient(clusterlink, "test", "api_test");
            //cli.setDebug(true);
            boolean resBool = false;
            long resLong = 0;
            String resStr = "";
            String key = prefix + "hk0";
            String field = "f0";
            String val = "v0";

            resBool = cli.hset(key, field, val);
            assertEquals(resBool, true);
            resStr = cli.hget(key, field);
            assertEquals(resStr, val);
            resBool = cli.hdel(key, field);
            assertEquals(resBool, true);
            resStr = cli.hget(key, field);
            assertEquals(resStr, null);

            key = prefix + "hk1";
            field = "f1";
            val = "888";
            resBool = cli.hexists(key, field);
            assertEquals(resBool, false);
            resLong = cli.hincr(key, field, 888);
            assertEquals(resLong, 888);
            resLong = cli.hdecr(key, field, 444);
            assertEquals(resLong, 444);
            resBool = cli.hexists(key, field);
            assertEquals(resBool, true);
            Map<String, String> resMap = cli.hgetall(key);
            assertEquals(resMap.get(field), "444");
            resLong = cli.hsize(key);
            assertEquals(resLong, 1);
            resBool = cli.hclear(key);
            assertEquals(resBool, true);
            //key = prefix + "hxxxx";
            resMap.clear();
            resMap = cli.hgetall(key);
            assertEquals(resMap.size(), 0);
            key = prefix + "hk3";
            Map<String, Object> kvs = new HashMap<String, Object> ();
            kvs.put("f0", "v0");
            kvs.put("f1", "v1");
            kvs.put("f2", "v2");
            String key4 = prefix + "hk4";
            resBool = cli.multi_hset(key, kvs);
            assertEquals(resBool, true);
            resBool = cli.multi_hset(key4, kvs);
            assertEquals(resBool, true);
            List<String> fields = new ArrayList<String>();
            fields.add("f0");
            fields.add("f1");
            fields.add("f2");
            resMap.clear();
            resMap = cli.multi_hget(key, fields);
            assertEquals(resMap.get("f0"), "v0");
            assertEquals(resMap.get("f1"), "v1");
            assertEquals(resMap.get("f2"), "v2");
            List<String> keys = new ArrayList<String>();
            keys.add(key);
            keys.add(key4);
            Map<String, Map<String, String>> resTable = cli.mhmget(keys, fields);
            assertEquals(resTable.get(key).get("f0"), "v0");
            assertEquals(resTable.get(key4).get("f2"), "v2");
            resBool = cli.multi_hdel(key, fields);
            assertEquals(resBool, true);
            key = prefix + "hk6";
            field = "f0";
            val = "v0";
            String val1 = "v1";
            resBool = cli.hset(key, field, val);
            assertEquals(resBool, true);
            resStr = cli.hset_if_eq(key, field, val1, "v2");
            assertEquals(resStr, val);
            resStr = cli.hset_if_eq(key, field, val1, val);
            assertEquals(resStr, null);
            resStr = cli.hget(key, field);
            assertEquals(resStr, val1);
            resBool = cli.hdel_if_eq(key, field, "v2");
            assertEquals(resBool, false);
            resBool = cli.hdel_if_eq(key, field, val1);
            assertEquals(resBool, true);
            resStr = cli.hget(key, field);
            assertEquals(resStr, null);

            kvs.clear();
            key = prefix + "hk10";
            for (int i = 100; i < 300; i++) {
                field = String.format("f%08d", i);
                val = String.format("v%08d", i);
                kvs.put(field, val);
            }
            resBool = cli.multi_hset(key, kvs);
            assertEquals(resBool, true);
            String start = String.format("f%08d", 100);
            String end = String.format("f%08d", 110);
            String xfield = String.format("f%08d", 105);
            String xval = String.format("v%08d", 105);
            resMap.clear();
            resMap = cli.hscan(key, start, end, 8);
            assertEquals(resMap.size(), 8);
            assertEquals(resMap.get(xfield), xval);
            List<String> resList = new ArrayList<String>();
            resList = cli.hkeys(key, start, end, 8);
            assertEquals(resList.size(), 8);
            assertEquals(resList.get(4), xfield);
            for (int i = 1000; i < 1100; i++) {
                key = prefix + String.format("h%08d", i);
                resBool = cli.hset(key, "f", "v");
                assertEquals(resBool, true);
            }
            start = prefix + String.format("h%08d", 1000);
            end = prefix + String.format("h%08d", 1100);
            String xkey = prefix + String.format("h%08d", 1080);
            resList.clear();
            resList = cli.hlist(start, end, 80);
            assertEquals(resList.size(), 80);
            assertEquals(resList.get(79), xkey);
        } catch (Exception e) {
            e.printStackTrace();
            assertEquals(false, true);
        }
    }

    @Test
    public void testZSET() {
        try {
            long t = System.currentTimeMillis() / 1000;
            String prefix = "agent_" + String.valueOf(t) + "_";
            ClusterLink clusterlink = new ClusterLink("10.10.105.5:10025", 1024);
            BanyanDBClient cli = new BanyanDBClient(clusterlink, "test", "api_test");
            //cli.setDebug(true);
            boolean resBool = false;
            long resLong = 0;
            String resStr = "";
            String key = prefix + "zk0";
            String member = "m0";
            long score = 88;

            resBool = cli.zset(key, member, score);
            assertEquals(resBool, true);
            resLong = cli.zget(key, member);
            assertEquals(resLong, score);
            resBool = cli.zdel(key, member);
            assertEquals(resBool, true);
            resLong = cli.zget(key, member);
            assertEquals(resLong, -1);

            key = prefix + "zk1";
            member = "f1";
            score = 888;
            resBool = cli.zexists(key, member);
            assertEquals(resBool, false);
            resLong = cli.zincr(key, member, 888);
            assertEquals(resLong, 888);
            resLong = cli.zdecr(key, member, 444);
            assertEquals(resLong, 444);
            resBool = cli.zexists(key, member);
            assertEquals(resBool, true);
            resLong = cli.zsize(key);
            assertEquals(resLong, 1);
            resBool = cli.zclear(key);
            assertEquals(resBool, true);
            key = prefix + "zk3";
            Map<String, Object> kvs = new HashMap<String, Object> ();
            kvs.put("m0", "0");
            kvs.put("m1", "1");
            kvs.put("m2", "2");
            resBool = cli.multi_zset(key, kvs);
            assertEquals(resBool, true);
            List<String> members = new ArrayList<String>();
            members.add("m0");
            members.add("m1");
            members.add("m2");
            Map<String, Long> resMap = cli.multi_zget(key, members);
            assertEquals((long)resMap.get("m0"), 0);
            assertEquals((long)resMap.get("m1"), 1);
            assertEquals((long)resMap.get("m2"), 2);
            resBool = cli.multi_zdel(key, members);
            assertEquals(resBool, true);

            kvs.clear();
            key = prefix + "zk10";
            for (int i = 100; i < 300; i++) {
                member = String.format("m%08d", i);
                score = i;
                kvs.put(member, score);
            }
            resBool = cli.multi_zset(key, kvs);
            assertEquals(resBool, true);
            resLong = cli.zcount(key, 100, 200);
            assertEquals(resLong, 101);
            member = String.format("m%08d", 100);
            long start = 100;
            long end = 110;
            String xmember= String.format("m%08d", 105);
            long xval = 105;
            List<String> resList = cli.zkeys(key, member, start, end, 8);
            assertEquals(resList.size(), 8);
            assertEquals(resList.get(4), xmember);
            resMap.clear();

            resMap = cli.zscan(key, member, start, end, 8);
            assertEquals(resMap.size(), 8);
            assertEquals((long)resMap.get(xmember), xval);
            for (int i = 1000; i < 1100; i++) {
                key = prefix + String.format("z%08d", i);
                resBool = cli.zset(key, "m", 8);
                assertEquals(resBool, true);
            }
            String zstart = prefix + String.format("z%08d", 1000);
            String zend = prefix + String.format("z%08d", 1100);
            String xkey = prefix + String.format("z%08d", 1080);
            resList.clear();
            resList = cli.zlist(zstart, zend, 80);
            assertEquals(resList.size(), 80);
            assertEquals(resList.get(79), xkey);
        } catch (Exception e) {
            e.printStackTrace();
            assertEquals(false, true);
        }
    }

    @Test
    public void testVSET() {
        try {
            long t = System.currentTimeMillis() / 1000;
            String prefix = "agent_" + String.valueOf(t) + "_";
            ClusterLink clusterlink = new ClusterLink("10.10.105.5:10025", 1024);
            BanyanDBClient cli = new BanyanDBClient(clusterlink, "test", "api_test");
            //cli.setDebug(true);
            VSetValue resVSet = new VSetValue(0, "");
            boolean resBool = false;
            long resLong = 0;
            String resStr = "";
            String key = prefix + "vk0";
            String member = "m0";
            long score = 88;
            String val = "8";

            resBool = cli.vset(key, member, score, val);
            assertEquals(resBool, true);
            resVSet = cli.vget(key, member);
            assertEquals(resVSet.getScore(), score);
            assertEquals(resVSet.getValue(), val);
            resBool = cli.vdel(key, member);
            assertEquals(resBool, true);
            resVSet = cli.vget(key, member);
            assertEquals(resVSet, null);

            key = prefix + "vk1";
            member = "f1";
            score = 888;
            val = "888";
            resBool = cli.vexists(key, member);
            assertEquals(resBool, false);
            resLong = cli.vincr(key, member, 888);
            assertEquals(resLong, 888);
            resLong = cli.vdecr(key, member, 444);
            assertEquals(resLong, 444);
            resBool = cli.vexists(key, member);
            assertEquals(resBool, true);
            resLong = cli.vsize(key);
            assertEquals(resLong, 1);
            resBool = cli.vclear(key);
            assertEquals(resBool, true);
            key = prefix + "vk3";
            Map<String, Object> kvs = new HashMap<String, Object> ();
            VSetValue vset = null;
            vset = new VSetValue(0, "0");
            kvs.put("m0", vset);
            vset = new VSetValue(1, "1");
            kvs.put("m1", vset);
            vset = new VSetValue(2, "2");
            kvs.put("m2", vset);
            resBool = cli.multi_vset(key, kvs);
            assertEquals(resBool, true);
            List<String> members = new ArrayList<String>();
            members.add("m0");
            members.add("m1");
            members.add("m2");
            Map<String, VSetValue> resMap = cli.multi_vget(key, members);
            assertEquals(resMap.get("m0").getScore(), 0);
            assertEquals(resMap.get("m1").getScore(), 1);
            assertEquals(resMap.get("m2").getScore(), 2);
            resBool = cli.multi_vdel(key, members);
            assertEquals(resBool, true);

            kvs.clear();
            key = prefix + "vk10";
            for (int i = 100; i < 300; i++) {
                member = String.format("m%08d", i);
                score = i;
                vset = new VSetValue(score, "xxx");
                kvs.put(member, vset);
            }
            resBool = cli.multi_vset(key, kvs);
            assertEquals(resBool, true);
            resLong = cli.vcount(key, 100, 200);
            assertEquals(resLong, 101);
            member = String.format("m%08d", 100);
            long start = 100;
            long end = 110;
            String xmember= String.format("m%08d", 105);
            long xval = 105;
            List<String> resList = cli.vkeys(key, member, start, end, 8);
            assertEquals(resList.size(), 8);
            assertEquals(resList.get(4), xmember);
            resMap.clear();

            resMap = cli.vscan(key, member, start, end, 8);
            assertEquals(resMap.size(), 8);
            assertEquals(resMap.get(xmember).getScore(), xval);
            for (int i = 1000; i < 1100; i++) {
                key = prefix + String.format("v%08d", i);
                resBool = cli.vset(key, "m", 8, "xxx");
                assertEquals(resBool, true);
            }
            String zstart = prefix + String.format("v%08d", 1000);
            String zend = prefix + String.format("v%08d", 1100);
            String xkey = prefix + String.format("v%08d", 1080);
            resList.clear();
            resList = cli.vlist(zstart, zend, 80);
            assertEquals(resList.size(), 80);
            assertEquals(resList.get(79), xkey);
        } catch (Exception e) {
            e.printStackTrace();
            assertEquals(false, true);
        }
    }

    @Test
    public void testQUEUE() {
        try {
            long t = System.currentTimeMillis() / 1000;
            String prefix = "agent_" + String.valueOf(t) + "_";
            ClusterLink clusterlink = new ClusterLink("10.10.105.5:10025", 1024);
            BanyanDBClient cli = new BanyanDBClient(clusterlink, "test", "api_test");
            //cli.setDebug(true);
            boolean resBool = false;
            long resLong = 0;
            String resStr = "";
            String key = prefix + "qk0";
            String member = "m1";

            resLong = cli.qpush_back(key, member);
            assertEquals(resLong, 1);
            List<String> members = new ArrayList<String>();
            members.add("m2");
            members.add("m3");
            members.add("m4");
            members.add("m5");
            resLong = cli.qpush_back(key, members);
            assertEquals(resLong, 5);
            resLong = cli.qsize(key);
            assertEquals(resLong, 5);
            resStr = cli.qpop_front(key);
            assertEquals(resStr, "m1");
            members.clear();
            members = cli.qpop_front(key, 2);
            assertEquals(members.size(), 2);
            resBool = cli.qclear(key);
            assertEquals(resBool, true);
        } catch (Exception e) {
            e.printStackTrace();
            assertEquals(false, true);
        }
    }

    @Test
    public void testJLIST() {
        try {
            long t = System.currentTimeMillis() / 1000;
            String prefix = "agent_" + String.valueOf(t) + "_";
            ClusterLink clusterlink = new ClusterLink("10.10.105.5:10025", 1024);
            BanyanDBClient cli = new BanyanDBClient(clusterlink, "test", "api_test");
            //cli.setDebug(true);
            boolean resBool = false;
            long resLong = 0;
            String resStr = "";
            String key = prefix + "jk0";
            String member = "m1";
            String val = "";

            resBool = cli.jlpush(key, "m1", 0);
            assertEquals(resBool, true);
            resBool = cli.jlpush(key, "m2", 0);
            assertEquals(resBool, true);
            resBool = cli.jlpush(key, "m3", 0);
            assertEquals(resBool, true);
            resBool = cli.jlpush(key, "m4", 0);
            assertEquals(resBool, true);
            resLong = cli.jllen(key);
            assertEquals(resLong, 4);
            resStr = cli.jlpop(key);
            assertEquals(resStr, "m1");
            resLong = cli.jllen(key);
            assertEquals(resLong, 3);
            resBool = cli.jldel_all(key);
            assertEquals(resBool, true);

            key = prefix + "jdk0";
            val = "xxx";
            resLong = cli.jdelay(1, "set", key, val);
            System.out.println(resLong);
            resStr = cli.get(key);
            assertEquals(resStr, null);
            Thread.sleep(2000);
            resStr = cli.get(key);
            assertEquals(resStr, val);

            key = prefix + "jdk1";
            val = "xxxx";
            resLong = cli.jdelay(1, "set", key, val);
            System.out.println(resLong);
            resStr = cli.get(key);
            assertEquals(resStr, null);
            resBool = cli.jcancel(resLong);
            assertEquals(resBool, true);
            Thread.sleep(2000);
            resStr = cli.get(key);
            assertEquals(resStr, null);
        } catch (Exception e) {
            e.printStackTrace();
            assertEquals(false, true);
        }
    }
}
