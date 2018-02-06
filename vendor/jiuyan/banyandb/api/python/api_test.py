import os
import sys
import time
import unittest
from sys import stdin, stdout
from banyan_api import BanyanClient


mcli = None
acli = None

class TestMaster(unittest.TestCase):
    def setUp(self):
        # todo: start other
        t = time.time()
        self.prefix = 'master_%u_' % t
        self.cli = mcli

    def tearDown(self):
        pass

    def test_kv(self):
        print "===test kv==="
        # set get del exist
        key = self.prefix + 'k0'
        res = self.cli.request('set', [key, 'v0'])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('get', [key])
        self.assertTrue(res.is_ok == True and res.res[1] == 'v0')
        res = self.cli.request('getset', [key, 'v1'])
        self.assertTrue(res.is_ok == True and res.res[1] == 'v0')
        res = self.cli.request('get', [key])
        self.assertTrue(res.is_ok == True and res.res[1] == 'v1')
        res = self.cli.request('del', [key])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('get', [key])
        self.assertEqual(res.is_not_found, True, 'test del fail')
        # incr decr exists getbit setbit
        key = self.prefix + 'k1'
        val = '888'
        res = self.cli.request('exists', [key])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = self.cli.request('incr', [key, val])
        self.assertTrue(res.is_ok == True and res.res[1] == val)
        res = self.cli.request('decr', [key, '444'])
        self.assertTrue(res.is_ok == True and res.res[1] == '444')
        res = self.cli.request('exists', [key])
        self.assertTrue(res.is_ok == True and res.val == 1)
        key = self.prefix + 'k2'
        res = self.cli.request('setbit', [key, '123456', '1'])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = self.cli.request('getbit', [key, '123456'])
        self.assertTrue(res.is_ok == True and res.val == 1, "test getbit fail")
        # multi_set multi_get multi_set
        keys = [self.prefix + 'k3', self.prefix + 'k4', self.prefix + 'k5']
        res = self.cli.request('multi_set', [keys[0], 'v3', keys[1], 'v4', keys[2], 'v5'])
        self.assertTrue(res.is_ok == True and res.val == len(keys))
        res = self.cli.request('multi_get', keys)
        self.assertTrue(res.is_ok == True and res.val[keys[0]] == 'v3' and res.val[keys[1]] == 'v4' and res.val[keys[2]] == 'v5')
        res = self.cli.request('multi_del', keys)
        self.assertTrue(res.is_ok == True and res.val == len(keys))
        keys = []
        params = []
        #for i in range(0, 1000000):
        for i in range(0, 1000):
            key = self.prefix + 'k---long_key_test---%08u' % i
            val = '---value---%08u' % i
            keys.append(key)
            params.append(key)
            params.append(val)
        res = self.cli.request('multi_set', params)
        self.assertTrue(res.is_ok == True)
        res = self.cli.request('multi_del', keys)
        self.assertTrue(res.is_ok == True)
        #substr strlen setnx
        key = self.prefix + 'k10'
        res = self.cli.request('set', [key, 'substr_strlen_setnx'])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('substr', [key, '0', '6'])
        self.assertTrue(res.is_ok == True and res.val == "substr")
        res = self.cli.request('substr', [key, '0', '-13'])
        self.assertTrue(res.is_ok == True and res.val == "substr")
        res = self.cli.request('substr', [key, '-19', '6'])
        self.assertTrue(res.is_ok == True and res.val == "substr")
        res = self.cli.request('substr', [key, '-19', '-13'])
        self.assertTrue(res.is_ok == True and res.val == "substr")
        res = self.cli.request('strlen', [key])
        self.assertTrue(res.is_ok == True and res.val == len('substr_strlen_setnx'))
        res = self.cli.request('setnx', [key, 'substr_strlen_setnx'])
        self.assertTrue(res.is_ok == True and res.val == 0)


    def test_kv_scan(self):
        print "===test kv scan==="
        # scan rscan keys rkeys
        params = []
        for i in range(10000, 999, -1):
            key = self.prefix + 'k%08u' % i
            val = 'v%08u' % i
            params.append(key)
            params.append(val)
        res = self.cli.request('multi_set', params)
        self.assertEqual(res.is_ok, True)
        start = self.prefix + 'k%08u' % 1000
        end = self.prefix + 'k%08u' % 1010
        val = 'v%08u' % 1002
        res = self.cli.request('scan', [start, end, '4'])
        self.assertTrue(res.is_ok == True and len(res.val) == 8 and res.val[2*2 - 1] == val)
        start = self.prefix + 'k%08u' % 2000
        end = self.prefix + 'k%08u' % 1000
        val = 'v%08u' % 1900
        res = self.cli.request('rscan', [start, end, '1024'])
        self.assertTrue(res.is_ok == True and len(res.val) == 2000 and res.val[100*2 -1] == val)
        start = self.prefix + 'k%08u' % 2000
        end = self.prefix + 'k%08u' % 3000
        key = self.prefix + 'k%08u' % 2500
        res = self.cli.request('keys', [start, end, '888'])
        self.assertTrue(res.is_ok == True and len(res.val) == 888 and res.val[499] == key)
        start = self.prefix + 'k%08u' % 4000
        end = self.prefix + 'k%08u' % 3000
        key = self.prefix + 'k%08u' % 3500
        res = self.cli.request('rkeys', [start, end, '1024'])
        self.assertTrue(res.is_ok == True and len(res.val) == 1000 and res.val[499] == key)

    def test_ttl(self):
        print "===test ttl=="
        key = self.prefix + 'k10'
        key2 = self.prefix + 'k20'
        res = self.cli.request('setx', [key, 'v10', '1'])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('setx', [key2, 'v10', '1'])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('setx', [key2, 'v20', '10'])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('get', [key])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('get', [key2])
        self.assertEqual(res.is_ok, True)
        time.sleep(1.5)
        res = self.cli.request('get', [key])
        self.assertEqual(res.is_not_found, True)
        res = self.cli.request('get', [key2])
        self.assertTrue(res.is_ok == True and res.val == 'v20')
        key = self.prefix + 'k11'
        res = self.cli.request('set', [key, 'v10'])
        res = self.cli.request('expire', [key, '1'])
        res = self.cli.request('get', [key])
        self.assertEqual(res.is_ok, True)
        time.sleep(1.5)
        res = self.cli.request('get', [key])
        self.assertEqual(res.is_not_found, True)

    def test_delay(self):
        print "===test delay==="
        key = self.prefix + "dk0"
        val = 'dv0'
        #options = "ns:%s,tab:%s,proto:by" % (self.cli.ns, self.cli.tab)
        #statements = '%d\n%s\n3\nset\n%d\n%s\n%d\n%s\n\n' % (len(options), options, len(key), key, len(val), val)
        #reqs, opts = self.cli.package_request('set', [key, val])
        #statements = self.cli.serizlize_request(reqs);
        res = self.cli.request('jdelay', ['set', key, val, '10'])
        self.assertEqual(res.is_ok, True)
        delay_seq = res.val
        res = self.cli.request('jget', ['%u' % delay_seq])
        #self.assertTrue(res.is_ok == True and res.val == statements)
        res = self.cli.request('jcancel', ['%u' % delay_seq])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('jget', ['%u' % delay_seq ])
        self.assertEqual(res.is_not_found, True)
        t = int((time.time() + 100.0) * 1000)
        res = self.cli.request('jclear', ['0', str(t)])
        self.assertEqual(res.is_ok, True)
        for i in range(0, 100):
            key = self.prefix + 'jk%u' % i
            val = 'jv%u' % i
            res = self.cli.request('jdelay', ['set', key, val, '10'])
            self.assertEqual(res.is_ok, True)
        res = self.cli.request('jscan', ['0', str(t)])
        self.assertTrue(res.is_ok == True and res.val == 100)
        res = self.cli.request('jclear', ['0', str(t)])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('jscan', ['0', str(t)])
        self.assertTrue(res.is_ok == True and res.val == 0)

    def test_hash(self):
        print "===test hash==="
        # hset hget hdel
        key = self.prefix + 'hk0'
        field = 'f0'
        val = 'v0'
        res = self.cli.request('hset', [key, field, val])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('hget', [key, field])
        self.assertTrue(res.is_ok == True and res.val == val)
        res = self.cli.request('hdel', [key, field])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('hget', [key, field])
        self.assertEqual(res.is_not_found, True)
        # hincr hdecr hexists hsize hgetall hclear
        key = self.prefix + 'hk1'
        field = 'f1'
        val = '888'
        res = self.cli.request('hexists', [key, field])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = self.cli.request('hincr', [key, field, val])
        self.assertTrue(res.is_ok == True and res.val == 888) 
        res = self.cli.request('hdecr', [key, field, '444'])
        self.assertTrue(res.is_ok == True and res.val == 444)
        res = self.cli.request('hexists', [key, field])
        self.assertTrue(res.is_ok == True and res.val == 1)
        key = self.prefix + 'hk2'
        self.cli.request('hset', [key, 'f1', 'v1'])
        self.cli.request('hset', [key, 'f2', 'v2'])
        self.cli.request('hset', [key, 'f3', 'v3'])
        res = self.cli.request('hgetall', [key])
        self.assertEqual(res.is_ok, True, 'test hgetall fail')
        self.assertTrue(res.val[1] == 'v1' and res.val[3] == 'v2' and res.val[5] == 'v3')
        res = self.cli.request('hsize', [key])
        self.assertTrue(res.is_ok == True and res.val == 3)
        # multi_hset multi_hget multi_hdel mhmget
        key = self.prefix + 'hk3'
        fields = ['f0', 'f1', 'f2']
        vals = ['v0', 'v1', 'v2']
        kvs = []
        for i in range(0, 3):
            kvs.append(fields[i])
            kvs.append(vals[i])
        #kvs.insert(0, key)
        params = [key]
        params.extend(kvs)
        res = self.cli.request('multi_hset', params)
        self.assertTrue(res.is_ok == True and res.val == len(fields))
        params = [key]
        params.extend(fields)
        res = self.cli.request('multi_hget', params)
        self.assertTrue(res.is_ok == True and res.val[fields[0]] == vals[0] and res.val[fields[1]] == vals[1] and res.val[fields[2]] == vals[2])
        key2 = self.prefix + 'hk4'
        params = [key2]
        params.extend(kvs)
        res = self.cli.request('multi_hset', params)
        self.assertEqual(res.is_ok, True)
        params = [[key, key2], fields]
        # mhmget issue
        res = self.cli.request('mhmget', params)
        kv1 = {'f0':'v0', 'f1':'v1', 'f2':'v2'}
        kv2 = {'f0':'v0', 'f1':'v1', 'f2':'v2'}
        #print res
        self.assertTrue(res.is_ok == True and res.val[key] == kv1 and res.val[key2] == kv2)
        params = [key]
        params.extend(fields)
        res = self.cli.request('multi_hdel', params)
        self.assertTrue(res.is_ok == True and res.val == len(fields))
        key = self.prefix + 'hk5'
        kvs = []
        for i in range(0, 10000):
            field = 'faaaaaaaaaaaaaaaaaaaaaaaaaaabbbbbbbbbbbbbbbbbbbccccccccccccccccccdddddddddddddddd%d' % i
            val = 'vaaaaaaaaaaaaaaaaaabbbbbbbbbbbbbbbbbbbbbccccccccccccccccddddddddddddddddddd%d' % i
            kvs.append(field)
            kvs.append(val)
        params = [key]
        params.extend(kvs)
        res = self.cli.request('multi_hset', params)
        self.assertEqual(res.is_ok, True)
        key = self.prefix + 'hk6'
        field = 'f0'
        val = 'v0'
        val1 = 'v1'
        res = self.cli.request('hset', [key, field, val])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('hset_if_eq', [key, field, val1, 'v2'])
        self.assertTrue(res.is_error == True and res.val == val)
        res = self.cli.request('hset_if_eq', [key, field, val1, val])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('hget', [key, field])
        self.assertTrue(res.is_ok == True and res.val == val1)
        res = self.cli.request('hdel_if_eq', [key, field, 'v2'])
        self.assertTrue(res.is_ok == True and res.val == "0")
        res = self.cli.request('hdel_if_eq', [key, field, val1])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('hget', [key, field])
        self.assertEqual(res.is_not_found, True)

    def test_hash_scan(self):
        print "===test hash scan==="
        # hscan hrscan hkeys hrkeys hlist hrlist
        key = self.prefix + 'hk10'
        kvs = []
        for i in range(10000, 999, -1):
            field = 'f%08u' % i
            val = 'v%08u' % i
            kvs.append(field)
            kvs.append(val)
        params = [key]
        params.extend(kvs)
        res = self.cli.request('multi_hset', params)
        self.assertEqual(res.is_ok, True)
        start = 'f%08u' % 1000
        end = 'f%08u' % 1010
        val = 'v%08u' % 1005
        res = self.cli.request('hscan', [key, start, end, '8'])
        self.assertTrue(res.is_ok == True and len(res.val) == 8*2 and res.val[5*2-1] == val)
        start = 'f%08u' % 2000
        end = 'f%08u' % 1000
        val = 'v%08u' % 1500
        res = self.cli.request('hrscan', [key, start, end, '1024'])
        self.assertTrue(res.is_ok == True and len(res.val) == 1000*2 and res.val[500*2-1] == val)
        start = 'f%08u' % 2000
        end = 'f%08u' % 3000
        field = 'f%08u' % 2600
        res = self.cli.request('hkeys', [key, start, end, '888'])
        self.assertTrue(res.is_ok == True and len(res.val) == 888 and res.val[599] == field)
        start = 'f%08u' % 4000
        end = 'f%08u' % 2000
        field = 'f%08u' % 3000
        res = self.cli.request('hrkeys', [key, start, end, '2048'])
        self.assertTrue(res.is_ok == True and len(res.val) == 2000 and res.val[999] == field)
        for i in range(100, 999):
            key = self.prefix + 'h%08u' % i
            res = self.cli.request('hset', [key, 'f', 'v'])
            self.assertEqual(res.is_ok, True)
        start = self.prefix + 'h%08u' % 100
        end = self.prefix + 'h%08u' % 200
        key = self.prefix + 'h%08u' % 160
        res = self.cli.request('hlist', [start, end, '80'])
        self.assertTrue(res.is_ok == True and len(res.val) == 80 and res.val[59] == key)
        start = self.prefix + 'h%08u' % 800
        end = self.prefix + 'h%08u' % 500
        key = self.prefix + 'h%08u' % 600
        res = self.cli.request('hrlist', [start, end, '1024'])
        self.assertTrue(res.is_ok == True and len(res.val) == 300 and res.val[199] == key)

    def test_zset(self):
        print "===test zset==="
        # zset zget zdel
        key = self.prefix + 'zk0'
        member = 'm0'
        val = '88'
        res = self.cli.request('zset', [key, member, val])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('zget', [key, member])
        self.assertTrue(res.is_ok == True and res.val == val)
        res = self.cli.request('zdel', [key, member])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('zget', [key, member])
        self.assertEqual(res.is_not_found, True)
        # zincr zdecr zsize zclear 
        key = self.prefix + 'zk1'
        member = 'f1'
        score = '888'
        res = self.cli.request('zexists', [key, member])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = self.cli.request('zincr', [key, member, score])
        self.assertTrue(res.is_ok == True and res.val == 888)
        res = self.cli.request('zdecr', [key, member, '444'])
        self.assertTrue(res.is_ok == True and res.val == 444)
        res = self.cli.request('zexists', [key, member])
        self.assertTrue(res.is_ok == True and res.val == 1)
        key = self.prefix + 'zk2'
        self.cli.request('zset', [key, 'm1', '1'])
        self.cli.request('zset', [key, 'm2', '2'])
        self.cli.request('zset', [key, 'm3', '3'])
        res = self.cli.request('zsize', [key])
        self.assertTrue(res.is_ok == True and res.val == 3)
        res = self.cli.request('zclear', [key])
        self.assertTrue(res.is_ok == True and res.val == 3)
        res = self.cli.request('zsize', [key])
        self.assertTrue(res.is_ok == True and res.val == 0)
        # multi_zset multi_zget multi_zdel
        key = self.prefix + 'zk3'
        members = ['m0', 'm1', 'm2']
        scores = ['0', '1', '2']
        kvs = []
        for i in range(0, 3):
            kvs.append(members[i])
            kvs.append(scores[i])
        params = [key]
        params.extend(kvs)
        res = self.cli.request('multi_zset', params)
        self.assertTrue(res.is_ok == True and res.val == len(members))
        params = [key]
        params.extend(members)
        res = self.cli.request('multi_zget', params)
        self.assertTrue(res.is_ok == True and res.val[members[0]] == scores[0] and res.val[members[1]] == scores[1] and res.val[members[2]] == scores[2])
        res = self.cli.request('multi_zdel', params)
        self.assertTrue(res.is_ok == True and res.val == len(members))
    
    def test_zset_scan(self):
        print "===test zset scan==="
        # zcount zscan zrscan zlist zrlist zrange zrrange
        key = self.prefix + 'zk10'
        kvs = []
        for i in range(10000, 999, -1):
            member = 'm%08u' % i
            score = '%u' % i
            kvs.append(member)
            kvs.append(score)
        params = [key]
        params.extend(kvs)
        res = self.cli.request('multi_zset', params)
        self.assertEqual(res.is_ok, True)
        # [start, end]
        res = self.cli.request('zcount', [key, '1000', '2000'])
        self.assertTrue(res.is_ok == True and res.val == 1001)
        member = 'm%08u' % 1000
        start = '1000'
        end = '1010'
        score = '1005'
        res = self.cli.request('zkeys', [key, member, start, end, '8'])
        self.assertTrue(res.is_ok == True and len(res.val) == 8)
        res = self.cli.request('zscan', [key, member, start, end, '8'])
        self.assertTrue(res.is_ok == True and len(res.val) == 16 and res.val[5*2 - 1] == score)
        member = 'm%08u' % 2000
        start = '2000'
        end = '1000'
        score = '1500'
        res = self.cli.request('zrscan', [key, member, start, end, '1024'])
        self.assertTrue(res.is_ok == True and len(res.val) == 2000 and res.val[500*2 - 1] == score)
        score = '1200'
        res = self.cli.request('zrange', [key, '100', '1000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 2000 and res.val[(100+1)*2 - 1] == score)
        score = '8000'
        res = self.cli.request('zrrange', [key, '1000', '4000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 8000 and res.val[(1000+1)*2 - 1] == score)
        res = self.cli.request('zremrangebyrank', [key, '1000', '2000'])
        self.assertTrue(res.is_ok == True and res.val == 1001)
        res = self.cli.request('zcount', [key, '2000', '3000'])
        self.assertTrue(res.is_ok == True and res.val == 0)
        for i in range(100, 999):
            key = self.prefix + 'z%08u' % i
            res = self.cli.request('zset', [key, 'm', '1'])
            self.assertEqual(res.is_ok, True)
        start = self.prefix + 'z%08u' % 100
        end = self.prefix + 'z%08u' % 200
        key = self.prefix + 'z%08u' % 160
        res = self.cli.request('zlist', [start, end, '80'])
        self.assertTrue(res.is_ok == True and len(res.val) == 80 and res.val[59] == key)
        start = self.prefix + 'z%08u' % 800
        end = self.prefix + 'z%08u' % 500
        key = self.prefix + 'z%08u' % 600
        res = self.cli.request('zrlist', [start, end, '1024'])
        self.assertTrue(res.is_ok == True and len(res.val) == 300 and res.val[199] == key)
        key = self.prefix + 'zk11'
        for i in range(10): #0-9
            self.cli.request("zset", [key, str(i), str(i)])
        res = self.cli.request("zsum", [key, "5", "9"])
        self.assertTrue(res.is_ok == True and res.val == 35)
        res = self.cli.request("zavg", [key, "5", "9"])
        self.assertTrue(res.is_ok == True and res.val == 7)
        res = self.cli.request("zsum", [key, "", "9"])
        self.assertTrue(res.is_ok == True and res.val == 45)
        res = self.cli.request("zavg", [key, "", "9"])
        self.assertTrue(res.is_ok == True and res.val == 4.5)
        res = self.cli.request("zsum", [key, "3", ""])
        self.assertTrue(res.is_ok == True and res.val == 42)
        res = self.cli.request("zavg", [key, "3", ""])
        self.assertTrue(res.is_ok == True and res.val == 6)
        res = self.cli.request("zsum", [key, "", ""])
        self.assertTrue(res.is_ok == True and res.val == 45)
        res = self.cli.request("zavg", [key, "", ""])
        self.assertTrue(res.is_ok == True and res.val == 4.5)
        res = self.cli.request("zsum", [key, "-9999999", "9999999"])
        self.assertTrue(res.is_ok == True and res.val == 45)
        res = self.cli.request("zavg", [key, "-9999999", "9999999"])
        self.assertTrue(res.is_ok == True and res.val == 4.5)
        res = self.cli.request("zsum", [key, "999999999", "99999999999999999"])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = self.cli.request("zsum", [key, "999999999", "99999999999999999"])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = self.cli.request("zrank", [key, "6"])
        self.assertTrue(res.is_ok == True and res.val == 6)
        res = self.cli.request("zrrank", [key, "6"])
        self.assertTrue(res.is_ok == True and res.val == 3)
        res = self.cli.request("zrank", [key, "66"])
        self.assertTrue(res.is_not_found == True)
        res = self.cli.request("zremrangebyscore", [key, '7','9'])
        self.assertTrue(res.is_ok == True and res.val == 3)
        res = self.cli.request("zscan", [key,'', '7', '9', '100'])
        self.assertTrue(res.is_ok == True and len(res.val) == 0)



    def test_vset(self):
        print "===test vset==="
        # vset vget vdel
        key = self.prefix + 'vk0'
        member = 'key0'
        value = 'value0'
        score = '88'
        res = self.cli.request('vset', [key, member, score, value])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('vget', [key, member])
        self.assertTrue(res.is_ok == True and res.val == [score, value])
        res = self.cli.request('vdel', [key, member])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('vget', [key, member])
        self.assertEqual(res.is_not_found, True)
        res = self.cli.request('vset', [key, member, score, value])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('vset_score', [key, member, '100'])
        self.assertTrue(res.is_ok == True)
        res = self.cli.request('vget', [key, member])
        self.assertTrue(res.is_ok == True and res.val[0] == '100' and res.val[1] == value)
        res = self.cli.request('vset_value', [key, member, 'value000'])
        self.assertTrue(res.is_ok == True)
        res = self.cli.request('vget', [key, member])
        self.assertTrue(res.is_ok == True and res.val[0] == '100' and res.val[1] == 'value000')

        # vincr vdecr vsize vclear
        key = self.prefix + 'vk1'
        member = 'key1'
        value = 'value1'
        score = '888'
        score_incr = '10'
        res = self.cli.request('vexists', [key, member])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = self.cli.request('vincr', [key, member, score_incr])
        self.assertTrue(res.is_ok == True and res.val == int(score_incr))
        res = self.cli.request('vdecr', [key, member, '444'])
        self.assertTrue(res.is_ok == True and res.val == -434)
        res = self.cli.request('vexists', [key, member])
        self.assertTrue(res.is_ok == True and res.val == 1)
        key = self.prefix + 'vk2'
        self.cli.request('vset', [key, 'm1', '1', 'v1'])
        self.cli.request('vset', [key, 'm2', '2', 'v2'])
        self.cli.request('vset', [key, 'm3', '3', 'v3'])
        res = self.cli.request('vsize', [key])
        self.assertTrue(res.is_ok == True and res.val == 3)
        res = self.cli.request('vclear', [key])
        self.assertTrue(res.is_ok == True and res.val == 3)
        res = self.cli.request('vsize', [key])
        self.assertTrue(res.is_ok == True and res.val == 0)
        # multi_vset multi_vget multi_vdel
        key = self.prefix + 'vk3'
        members = ['m0', 'm1', 'm2']
        values =   ['v0', 'v1', 'v2']
        scores = ['0', '1', '2']
        kvs = []
        for i in range(0, 3):
            kvs.append(members[i])
            kvs.append(scores[i])
            kvs.append(values[i])
        params = [key]
        params.extend(kvs)
        res = self.cli.request('multi_vset', params)
        self.assertTrue(res.is_ok == True and res.val == len(members))
        params = [key]
        params.extend(members)
        res = self.cli.request('multi_vget', params)
        self.assertTrue(res.is_ok == True and res.val == [members[0],scores[0],values[0],members[1],scores[1],values[1],members[2],scores[2],values[2]])
        res = self.cli.request('multi_vdel', params)
        self.assertTrue(res.is_ok == True and res.val == len(members))

    def test_vset_scan(self):
        print "===test vset scan==="
        # vcount vscan vrscan vlist vrlist vrange vrrange
        key = self.prefix + 'vk10'
        kvs = []
        for i in range(10000, 999, -1):
            member = 'm%08u' % i
            value = 'value%u' %i
            score = '%u' % i
            kvs.append(member)
            kvs.append(score)
            kvs.append(value)
        params = [key]
        params.extend(kvs)
        res = self.cli.request('multi_vset', params)
        self.assertEqual(res.is_ok, True)
        # [start, end]
        res = self.cli.request('vcount', [key, '1000', '2000'])
        self.assertTrue(res.is_ok == True and res.val == 1001)
        member = 'm%08u' % 1000
        start = '1000'
        end = '1010'
        score = '1005'

        res = self.cli.request('vkeys', [key, member, start, end, '8'])
        self.assertTrue(res.is_ok == True and len(res.val) == 8)
        res = self.cli.request('vscan', [key, member, start, end, '8'])
        self.assertTrue(res.is_ok == True and len(res.val) == 8*3 and res.val[5*3 - 2] == score)
        member = 'm%08u' % 2000
        start = '2000'
        end = '1000'
        score = '1500'
        res = self.cli.request('vrscan', [key, member, start, end, '1024'])
        self.assertTrue(res.is_ok == True and len(res.val) == 1000*3 and res.val[500*3 - 2] == score)
        score = '1200'
        res = self.cli.request('vrange', [key, '100', '1000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 1000*3 and res.val[(100+1)*3 - 2] == score)
        score = '8000'
        res = self.cli.request('vrrange', [key, '1000', '4000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 4000*3 and res.val[(1000+1)*3 - 2] == score)
        res = self.cli.request('vremrangebyrank', [key, '1000', '2000'])
        self.assertTrue(res.is_ok == True and res.val == 1001)
        res = self.cli.request('vcount', [key, '2000', '3000'])
        self.assertTrue(res.is_ok == True and res.val == 0)
        for i in range(100, 999):
            key = self.prefix + 'v%08u' % i
            res = self.cli.request('vset', [key, 'm', '1','v'])
            self.assertEqual(res.is_ok, True)
        start = self.prefix + 'v%08u' % 100
        end = self.prefix + 'v%08u' % 200
        key = self.prefix + 'v%08u' % 160
        res = self.cli.request('vlist', [start, end, '80'])
        self.assertTrue(res.is_ok == True and len(res.val) == 80 and res.val[59] == key)
        start = self.prefix + 'v%08u' % 800
        end = self.prefix + 'v%08u' % 500
        key = self.prefix + 'v%08u' % 600
        res = self.cli.request('vrlist', [start, end, '1024'])
        self.assertTrue(res.is_ok == True and len(res.val) == 300 and res.val[199] == key)
        key = self.prefix + 'vk11'
        for i in range(10): #0-9
            self.cli.request("vset", [key, str(i), str(i), str(i)])
        res = self.cli.request("vsum", [key, "5", "9"])
        self.assertTrue(res.is_ok == True and res.val == 35)
        res = self.cli.request("vavg", [key, "5", "9"])
        self.assertTrue(res.is_ok == True and res.val == 7)
        res = self.cli.request("vsum", [key, "", "9"])
        self.assertTrue(res.is_ok == True and res.val == 45)
        res = self.cli.request("vavg", [key, "", "9"])
        self.assertTrue(res.is_ok == True and res.val == 4.5)
        res = self.cli.request("vsum", [key, "3", ""])
        self.assertTrue(res.is_ok == True and res.val == 42)
        res = self.cli.request("vavg", [key, "3", ""])
        self.assertTrue(res.is_ok == True and res.val == 6)
        res = self.cli.request("vsum", [key, "", ""])
        self.assertTrue(res.is_ok == True and res.val == 45)
        res = self.cli.request("vavg", [key, "", ""])
        self.assertTrue(res.is_ok == True and res.val == 4.5)
        res = self.cli.request("vsum", [key, "-9999999", "9999999"])
        self.assertTrue(res.is_ok == True and res.val == 45)
        res = self.cli.request("vavg", [key, "-9999999", "9999999"])
        self.assertTrue(res.is_ok == True and res.val == 4.5)
        res = self.cli.request("vsum", [key, "999999999", "99999999999999999"])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = self.cli.request("vsum", [key, "999999999", "99999999999999999"])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = self.cli.request("vrank", [key, "6"])
        self.assertTrue(res.is_ok == True and res.val == 6)
        res = self.cli.request("vrrank", [key, "6"])
        self.assertTrue(res.is_ok == True and res.val == 3)
        res = self.cli.request("vrank", [key, "66"])
        self.assertTrue(res.is_not_found == True)
        res = self.cli.request("vremrangebyscore", [key, '7','9'])
        self.assertTrue(res.is_ok == True and res.val == 3)
        res = self.cli.request("vscan", [key,'', '7', '9', '100'])
        self.assertTrue(res.is_ok == True and len(res.val) == 0)

    def test_queue(self):
        print "===test queue==="
        # qsize qpop qpush qclear qslice qrange
        key = self.prefix + 'qk0'
        for i in range(0, 1000):
            val = '%u' % i
            res = self.cli.request('qpush', [key, val])
            self.assertEqual(res.is_ok, True)
        res = self.cli.request('qpush', [key, '1000', '1001', '1002'])
        self.assertTrue(res.is_ok == True and res.val == 1003)
        res = self.cli.request('qsize', [key])
        self.assertTrue(res.is_ok == True and res.val == 1003)
        res = self.cli.request('qpop', [key])
        self.assertTrue(res.is_ok == True and res.val[0] == '0')
        res = self.cli.request('qpop', [key, '2'])
        self.assertTrue(res.is_ok == True and res.val[0] == '1' and res.val[1] == '2')
        res = self.cli.request('qsize', [key])
        self.assertTrue(res.is_ok == True and res.val == 1000)
        res = self.cli.request('qclear', [key])
        res = self.cli.request('qsize', [key])
        self.assertTrue(res.is_ok == True and res.val == 0)
        vals = []
        for i in range(0, 1000):
            vals.append('%u' % i)
        params = [key]
        params.extend(vals)
        res = self.cli.request('qpush', params)
        self.assertTrue(res.is_ok == True, res.val == 1000)
        val = '120'
        # [start, end]
        res = self.cli.request('qslice', [key, '100', '200'])
        self.assertTrue(res.is_ok == True and len(res.val) == 101 and res.val[20] == val)
        val = '880'
        res = self.cli.request('qrange', [key, '800', '100'])
        self.assertTrue(res.is_ok == True and len(res.val) == 100 and res.val[80] == val)
        for i in range(100, 999):
            key = self.prefix + 'q%08u' % i
            res = self.cli.request('qpush', [key, 'item'])
            self.assertEqual(res.is_ok, True)
        start = self.prefix + 'q%08u' % 100
        end = self.prefix + 'q%08u' % 200
        key = self.prefix + 'q%08u' % 160
        res = self.cli.request('qlist', [start, end, '80'])
        self.assertTrue(res.is_ok == True and len(res.val) == 80 and res.val[59] == key)
        start = self.prefix + 'q%08u' % 800
        end = self.prefix + 'q%08u' % 500
        key = self.prefix + 'q%08u' % 600
        res = self.cli.request('qrlist', [start, end, '1024'])
        self.assertTrue(res.is_ok == True and len(res.val) == 300 and res.val[199] == key)
        key = self.prefix + 'queue_test'
        res = self.cli.request('qpush_front', [key, 'key1', 'key2'])
        self.assertTrue(res.is_ok == True and res.val == 2)
        res = self.cli.request('qpush', [key, 'key3', 'key4'])
        self.assertTrue(res.is_ok == True and res.val == 4)
        res = self.cli.request('qfront', [key])
        self.assertTrue(res.is_ok == True and res.val == 'key2')
        res = self.cli.request('qback', [key])
        self.assertTrue(res.is_ok == True and res.val == 'key4')
        res = self.cli.request('qget', [key,'0'])
        self.assertTrue(res.is_ok == True and res.val == 'key2')
        res = self.cli.request('qset', [key,'0', 'key2~'])
        self.assertTrue(res.is_ok == True)
        res = self.cli.request('qpop_back', [key])
        self.assertTrue(res.is_ok == True and res.val == ['key4'])
        res = self.cli.request('qtrim_back', [key])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('qtrim_front', [key])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = self.cli.request('qsize', [key])
        self.assertTrue(res.is_ok == True and res.val == 1)

    def test_jlist(self):
        print "===test jlist==="
        # jllen jlpop jlpush jlslice jlgetd jlsetd jldeld jldel_all jllist
        key = self.prefix + 'jlk0'
        for i in range(0, 1000):
            val = '%u' % i
            res = self.cli.request('jlpush', [key, val, '0'])
            self.assertEqual(res.is_ok, True)
        res = self.cli.request('jllen', [key])
        self.assertTrue(res.is_ok == True and res.val == 1000)
        res = self.cli.request('jlpop', [key])
        self.assertTrue(res.is_ok == True and res.val == '0')
        res = self.cli.request('jllen', [key])
        self.assertTrue(res.is_ok == True and res.val == 999)
        res = self.cli.request('jlslice', [key, '100', '10'])
        self.assertTrue(res.is_ok == True and len(res.val) == 20)
        seq_val_list = res.val
        val = 'xxx'
        res = self.cli.request('jlsetd', [key, seq_val_list[0], val])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('jlgetd', [key, seq_val_list[0]])
        self.assertTrue(res.is_ok == True and res.val == val)
        res = self.cli.request('jldeld', [key, seq_val_list[0], val])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('jlgetd', [key, seq_val_list[0]])
        self.assertEqual(res.is_not_found, True)
        res = self.cli.request('jllen', [key])
        self.assertTrue(res.is_ok == True and res.val == 998)
        res = self.cli.request('jldel_all', [key])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('jllen', [key])
        self.assertTrue(res.is_ok == True and res.val == 0)
        key = self.prefix + 'jlk1'
        val = 'x'
        res = self.cli.request('jlpush', [key, val, '1'])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('jlpush', [key, val, '1'])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('jllen', [key])
        self.assertTrue(res.is_ok == True and res.val == 2)
        time.sleep(2)
        res = self.cli.request('jllen', [key])
        self.assertTrue(res.is_ok == True and res.val == 0)
        for i in range(100, 999):
            key = self.prefix + 'jl%08u' % i
            res = self.cli.request('jlpush', [key, 'item', '0'])
            self.assertEqual(res.is_ok, True)
        start = self.prefix + 'jl%08u' % 100
        end = self.prefix + 'jl%08u' % 200
        key = self.prefix + 'jl%08u' % 160
        res = self.cli.request('jllist', [start, end, '80'])
        self.assertTrue(res.is_ok == True and len(res.val) == 80 and res.val[59] == key)
        

class TestAgent(TestMaster):
    def setUp(self):
        t = time.time()
        self.prefix = 'agent_%u_' % t
        self.cli = acli
        #self.cli.change_namespace('test')
        #self.cli.change_table('api_test')

    def tearDown(self):
        pass

#class TestTrigger(unittest.TestCase):
class TestTrigger():
    def setUp(self):
        t = time.time()
        self.prefix = 'trigger_%u_' % t
        self.cli = BanyanClient(agent, 'test', 'api_test')

    def tearDown(self):
        pass

    def test_jdelay(self):
        print "===test jdelay==="
        key = self.prefix + 'j0'
        val = 'j0'
        options = "ns:%s,tab:%s,proto:by" % (self.cli.ns, self.cli.tab)
        statements = '%d\n%s\n3\nset\n%d\n%s\n%d\n%s\n\n' % (len(options), options, len(key), key, len(val), val)
        res = self.cli.request('jdelay', ['set', key, val, '1'])
        self.assertEqual(res.is_ok, True)
        delay_seq = res.val
        res = self.cli.request('jget', ['%d' % delay_seq])
        key1 = self.prefix + 'j1'
        val1 = 'j1'
        res = self.cli.request('jdelay', ['set', key1, val1, '1'])
        self.assertEqual(res.is_ok, True)
        #print res.val
        #self.assertTrue(res.is_ok == True and res.val == statements)
        time.sleep(3)
        res = self.cli.request('get', [key])
        self.assertTrue(res.is_ok == True and res.val == val)
        

if __name__ == '__main__':
    master = ['10.10.105.5:10030']
    agent = ['10.10.105.5:10200']
    # start server
    '''
    cmd = 'killall chunkserver agent trigger'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    time.sleep(5)
    cmd = 'cd /home/letian/banyandb/agent0/;bin/agent&'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    cmd = 'cd /home/letian/banyandb/chunkserver_master/;bin/chunkserver&'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    cmd = 'cd /home/letian/banyandb/trigger0/;bin/trigger&'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    '''
    time.sleep(2)
    acli = BanyanClient(agent, 'test', 'api_test')
    mcli = BanyanClient(master, 'test', 'api_test')
    unittest.main()
