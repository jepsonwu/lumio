import time
import os
import unittest
from banyan_api import BanyanClient


class TestCache(unittest.TestCase):
    def setUp(self):
        t = int(time.time())
        self.prefix = '%d_' % t
        self.suffix = '_agent_cache_%u' % t
        self.agent_dir = '/home/letian/banyandb/agent0/'
        self.mchunkserver_dir = '/home/letian/banyandb/chunkserver_master/'
        cmd = 'killall agent chunkserver'
        time.sleep(3)

    def tearDown(self):
        cmd = 'killall agent chunkserver'
        status = os.system(cmd)
        print 'cmd:%s status:%d' % (cmd, status)

    def test_cache(self):
        print "===test cache==="
        print 'start agent'
        cmd = 'cd %s;bin/agent&' % (self.agent_dir)
        status = os.system(cmd)
        self.assertEqual(status, 0)
        time.sleep(1)
        ipport = ['127.0.0.1:10024']
        agent_cli = BanyanClient(ipport, 'test', 'api_test')
        for i in range(0, 1000):
            key = self.prefix + 'k%u' % i
            val = '%u' % i
            res = agent_cli.request('set', [key, val])
            self.assertEqual(res.is_buffer, True)
        key = self.prefix + 'k%u' % 10
        res = agent_cli.request('get', [key])
        self.assertEqual(res.is_error, True)

        key1 = '888' + self.suffix
        val1 = '888'
        key2 = 'ABCD' + self.suffix
        val2 = 'ABCD'
        key3 = 'abcd' + self.suffix
        val3 = 'abcd'
        res = agent_cli.request('multi_set', [key1, val1, key2, val2, key3, val3])
        self.assertEqual(res.is_buffer, True)
        print 'start master chunkserver'
        cmd = 'cd %s;bin/chunkserver&' % self.mchunkserver_dir
        status = os.system(cmd)
        self.assertEqual(status, 0)
        time.sleep(5)
        ipport = ['127.0.0.1:10025']
        chunkserver_cli = BanyanClient(ipport, 'test', 'api_test')
        for i in range(0, 1000):
            key = self.prefix + 'k%u' % i
            val = '%u' % i
            res = chunkserver_cli.request('get', [key])
            self.assertTrue(res.is_ok == True and res.val == val)
        res = chunkserver_cli.request('multi_get', [key1, key2, key3])
        self.assertTrue(res.is_ok == True and res.val[key1] == val1 and res.val[key2] == val2 and res.val[key3] == val3)

if __name__ == '__main__':
    unittest.main()
