import time
import unittest
from banyan_api import BanyanClient

mchunkserver = ['127.0.0.1:10025']
cli = BanyanClient(mchunkserver, 'test', 'api_test')
prefix = '%d_' % int(time.time())

class TestIssue(unittest.TestCase):
    def test_chunkserver_issue(self):
        print '===test issue=='
        key = prefix + 'k0'
        res = cli.request('setbit', [key, '123456', '1'])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = cli.request('getbit', [key, '123456'])
        self.assertTrue(res.is_ok == True and res.val == 1)
        key = prefix + 'zk1'
        kvs = []
        for i in range(10000, 999, -1):
            member = 'm%08u' % i
            score = '%u' % i
            kvs.append(member)
            kvs.append(score)
        params = [key]
        params.extend(kvs)
        res = cli.request('multi_zset', params)
        self.assertEqual(res.is_ok, True)
        # [start, end]
        res = cli.request('zcount', [key, '1000', '2000'])
        self.assertTrue(res.is_ok == True and res.val == 1001)
        key = prefix + 'q0'
        vals = []
        for i in range(0, 1000):
            vals.append('%u' % i)
        params = [key]
        params.extend(vals)
        res = cli.request('qpush', params)
        self.assertTrue(res.is_ok == True and res.val == 1000)
        val = '120'
        # [start, end]
        res = cli.request('qslice', [key, '100', '200'])
        self.assertTrue(res.is_ok == True and len(res.val) == 101 and res.val[20] == val)
        val = '880'
        res = cli.request('qrange', [key, '800', '100'])
        self.assertTrue(res.is_ok == True and len(res.val) == 100 and res.val[80] == val)
        key1 = prefix + 'hk3'
        fields = ['f0', 'f1', 'f2']
        vals = ['v0', 'v1', 'v2']
        kvs = []
        for i in range(0, 3):
            kvs.append(fields[i])
            kvs.append(vals[i])
        params = [key1]
        params.extend(kvs)
        res = cli.request('multi_hset', params)
        self.assertEqual(res.is_ok, True)
        key2 = prefix + 'hk4'
        params = [key2]
        params.extend(kvs)
        res = cli.request('multi_hset', params)
        self.assertEqual(res.is_ok, True)
        params = [[key1, key2], fields]
        res = cli.request('mhmget', params)
        kv1 = {'f0':'v0', 'f1':'v1', 'f2':'v2'}
        kv2 = {'f0':'v0', 'f1':'v1', 'f2':'v2'}
        self.assertTrue(res.is_ok == True and res.val[key1] == kv1 and res.val[key2] == kv2)
        key4 = prefix + 'hk4'
        res = cli.request('hincr', [key4, 'v0'])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = cli.request('hincr', [key4, 'v0', '-1'])
        self.assertTrue(res.is_ok == True and res.val == 0)
        key5 = prefix + 'zk5'
        res = cli.request('zincr', [key5, 'v0'])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = cli.request('zdecr', [key5, 'v0'])
        self.assertTrue(res.is_ok == True and res.val == 0)
        res = cli.request('zdecr', [key5, 'v0', '-1'])
        self.assertTrue(res.is_ok == True and res.val == 1)
        key6 = prefix + 'k6'
        res = cli.request('incr', [key6])
        self.assertTrue(res.is_ok == True and res.val == 1)
        res = cli.request('decr', [key6, '-2'])
        self.assertTrue(res.is_ok == True and res.val == 3)
        res = cli.request('decr', [key6])
        self.assertTrue(res.is_ok == True and res.val == 2)
        res = cli.request('incr', [key6, '-2'])
        self.assertTrue(res.is_ok == True and res.val == 0)

    def test_agent_issue(self):
        key = prefix + 'k0'
        #res = cli.request('set', [key, 'xxxx'])
        #print res
        res = cli.request('get', [key])
        self.assertTrue(res.is_not_found, True)

if __name__ == '__main__':
    unittest.main()
