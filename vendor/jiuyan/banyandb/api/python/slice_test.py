import os
import time
import unittest
from banyan_api import BanyanClient


class TestSlice(unittest.TestCase):
    def setUp(self):
        cmd = 'killall chunkserver agent'
        status = os.system(cmd)
        print 'cmd:%s status:%s' % (cmd, status)
        time.sleep(3)
        cmd = 'cd /home/letian/banyandb/chunkserver_master/;rm -rf data/ meta/;bin/chunkserver&'
        status = os.system(cmd)
        print 'cmd:%s status:%d' % (cmd, status)
        cmd = 'cd /home/letian/banyandb/agent0/;rm -rf data/ meta/;bin/agent&'
        status = os.system(cmd)
        print 'cmd:%s status:%d' % (cmd, status)
        time.sleep(2)
        t = time.time()
        self.suffix = '_agent_%u' % t
        agent = ['127.0.0.1:10024']
        self.cli = BanyanClient(agent, 'test', 'slice_test')
        pass

    def tearDown(self):
        cmd = 'killall chunkserver agent'
        status = os.system(cmd)
        print 'cmd:%s status:%s' % (cmd, status)
        time.sleep(1)
        pass

    def test_slice(self):
        key1 = '888' + self.suffix
        val1 = '888'
        key2 = 'ABCD' + self.suffix
        val2 = 'ABCD'
        key3 = 'abcd' + self.suffix
        val3 = 'abcd'
        res = self.cli.request('multi_set', [key1, val1, key2, val2, key3, val3])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('multi_get', [key1, key2, key3])
        self.assertTrue(res.is_ok == True and res.val[key1] == val1 and res.val[key2] == val2 and res.val[key3] == val3)
        res = self.cli.request('multi_del', [key1, key2, key3])
        self.assertEqual(res.is_ok, True)
        res = self.cli.request('multi_get', [key1, key2, key3])
        self.assertTrue(res.is_ok == True 
            and res.val.has_key(key1) == False and res.val.has_key(key2) == False and res.val.has_key(key3) == False)
        params = []
        for i in range(0, 1000):
            key = '%08u' % i + self.suffix
            val = '%08u' % i
            params.append(key)
            params.append(val)
        for i in range(0, 1000):
            key = 'B%08u' % i + self.suffix
            val = '%08u' % i
            params.append(key)
            params.append(val)
        for i in range(0, 1000):
            key = 'b%08u' % i + self.suffix
            val = '%08u' % i
            params.append(key)
            params.append(val)
        res = self.cli.request('multi_set', params)
        self.assertEqual(res.is_ok, True)
        start = '%08u' % 500 + self.suffix
        end = 'b%08u' % 800 + self.suffix
        res = self.cli.request('scan', [start, end, '2000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 4000)
        res = self.cli.request('keys', [start, end, '2000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 2000)
        start = 'b%08u' % 800 + self.suffix
        end = '%08u' % 800 + self.suffix
        res = self.cli.request('rscan', [start, end, '4000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 4000)
        res = self.cli.request('rkeys', [start, end, '4000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 2000)
        for i in range(0, 1000):
            key = '%08u' % i + self.suffix
            field1 = 'f0'
            val1 = 'v0%u' % i
            field2 = 'f1'
            val2 = 'v1%u' % i
            res = self.cli.request('multi_hset', [key, field1, val1, field2, val2])
            self.assertEqual(res.is_ok, True)
        for i in range(0, 1000):
            key = 'H%08u' % i + self.suffix
            field1 = 'f0'
            val1 = 'v0%u' % i
            field2 = 'f1'
            val2 = 'v1%u' % i
            res = self.cli.request('multi_hset', [key, field1, val1, field2, val2])
            self.assertEqual(res.is_ok, True)
        for i in range(0, 1000):
            key = 'h%08u' % i + self.suffix
            field1 = 'f0'
            val1 = 'v0%u' % i
            field2 = 'f1'
            val2 = 'v1%u' % i
            res = self.cli.request('multi_hset', [key, field1, val1, field2, val2])
            self.assertEqual(res.is_ok, True)
        start = '%08u' % 500 + self.suffix
        end = 'h%08u' % 800 + self.suffix
        res = self.cli.request('hlist', [start, end, '2000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 2000)
        start = 'h%08u' % 800 + self.suffix
        end = '%08u' % 800 + self.suffix
        res = self.cli.request('hrlist', [start, end, '2000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 2000)
        keys = []
        for i in range(80, 88):
            key1 = '%08u' % i + self.suffix
            key2 = 'H%08u' % i + self.suffix
            key3 = 'h%08u' % i + self.suffix
            keys.append(key1)
            keys.append(key2)
            keys.append(key3)
        fields = ['f0', 'f1']
        res = self.cli.request('mhmget', [keys, fields])
        key1 = '%08u' % 84 + self.suffix
        key2 = 'H%08u' % 84 + self.suffix
        key3 = 'h%08u' % 84 + self.suffix
        kvs = {'f0':'v084', 'f1':'v184'}
        print res.val
        self.assertTrue(res.is_ok == True and res.val[key1] == kvs and res.val[key2] == kvs and res.val[key3] == kvs)
        for i in range(0, 1000):
            key = '%08u' % i + self.suffix
            score = '%u' % i
            val = '%08u' % i
            res = self.cli.request('zset', [key, score, val])
            self.assertEqual(res.is_ok, True)
        for i in range(0, 1000):
            key = 'Y%08u' % i + self.suffix
            score = '%u' % i
            val = '%08u' % i
            res = self.cli.request('zset', [key, score, val])
            self.assertEqual(res.is_ok, True)
        for i in range(0, 1000):
            key = 'y%08u' % i + self.suffix
            score = '%u' % i
            val = '%08u' % i
            res = self.cli.request('zset', [key, score, val])
            self.assertEqual(res.is_ok, True)
        start = '%08u' % 500 + self.suffix
        end = 'z%08u' % 800 + self.suffix
        res = self.cli.request('zlist', [start, end, '2000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 2000)
        start = 'z%08u' % 800 + self.suffix
        end = '%08u' % 800 + self.suffix
        res = self.cli.request('zrlist', [start, end, '2000'])
        self.assertTrue(res.is_ok == True and len(res.val) == 2000)


if __name__ == '__main__':
    unittest.main()
