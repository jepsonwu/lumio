import time
import os
import unittest
from banyan_api import BanyanClient

class TestSilceSplit(unittest.TestCase):
    def setUp(self):
        t = time.time();
        self.prefix = 'slice_split_test_%u_' % t

    def tearDown(self):
        pass

    def test_find_key(self):
        return
        print "===test find key==="
        ns = 'test'
        table = 'api_test'
        master_ipport = ['10.10.105.5:10025']
        mcli = BanyanClient(master_ipport, 'test', 'api_test')
        res = mcli.request('zk_slice_find_key', [], 'slice:0')
        self.assertEqual(res.is_ok, True)
        #print res

    def test_slice_split(self):
        print "===test slice split==="
        ns = 'test'
        table = 'api_test'
        master_ipport = ['127.0.0.1:10025']
        slave_ipport = ['127.0.0.1:10026']
        mcli = BanyanClient(master_ipport, 'test', 'api_test')
        res = mcli.request('zk_slice_split', ["q", '1', '2'], 'slice:0')
        self.assertEqual(res.is_ok, True)
        mcli = BanyanClient(master_ipport, 'test', 'api_test')
        res = mcli.request('slice_info', [ns, table])
        print res.val
        key = self.prefix + "k0"
        val = "master_split"
        res = mcli.request('set', [key, val])
        self.assertEqual(res.is_ok, True)
        time.sleep(1)
        scli = BanyanClient(slave_ipport, 'test', 'api_test')
        res = scli.request('get', [key])
        self.assertTrue(res.is_ok == True and res.val == val)
        print "===split master ok==="
        return

        res = scli.request('zk_slice_split', ["h", '1', '2'], 'slice:0')
        self.assertEqual(res.is_ok, True)
        scli = BanyanClient(slave_ipport, 'test', 'api_test')
        res = scli.request('slice_info', [ns, table])
        print res.val
        key = self.prefix + "k1"
        val = "slave_split"
        res = mcli.request('set', [key, val])
        self.assertEqual(res.is_ok, True)
        time.sleep(1)
        scli = BanyanClient(slave_ipport, 'test', 'api_test')
        res = scli.request('get', [key])
        self.assertTrue(res.is_ok == True and res.val == val)
        print "===split slave ok===="

        mcli = BanyanClient(master_ipport, 'test', 'api_test')
        res = mcli.request('zk_split_status_reverse', ['1', '2'], 'slice:0')
        self.assertEqual(res.is_ok, True)
        mcli = BanyanClient(master_ipport, 'test', 'api_test')
        res = mcli.request('zk_slice_delete', [], 'slice:0')
        self.assertEqual(res.is_ok, True)
        mcli = BanyanClient(master_ipport, 'test', 'api_test')
        res = mcli.request('slice_info', [ns, table])
        print res.val
        scli = BanyanClient(slave_ipport, 'test', 'api_test')
        res = scli.request('zk_split_status_reverse', ['1', '2'], 'slice:0')
        self.assertEqual(res.is_ok, True)
        scli = BanyanClient(slave_ipport, 'test', 'api_test')
        res = scli.request('zk_slice_delete', [], 'slice:0')
        self.assertEqual(res.is_ok, True)
        scli = BanyanClient(slave_ipport, 'test', 'api_test')
        res = scli.request('slice_info', [ns, table])
        print res.val

        key = self.prefix + "k3"
        val = "slice_delete"
        mcli = BanyanClient(master_ipport, 'test', 'api_test')
        res = mcli.request('set', [key, val])
        self.assertEqual(res.is_ok, True)
        time.sleep(1)
        scli = BanyanClient(slave_ipport, 'test', 'api_test')
        res = scli.request('get', [key])
        self.assertTrue(res.is_ok == True and res.val == val)
        print "===slice delete ok===="
        res = mcli.request('bseqget', [], 'slice:1')
        print res.val
        res = mcli.request('bseqget', [], 'slice:2')
        print res.val
        res = mcli.request('bseqget', [], 'slice:0')
        print res
        res = scli.request('bseqget', [], 'slice:1')
        print res.val
        res = scli.request('bseqget', [], 'slice:2')
        print res.val
        res = scli.request('bseqget', [], 'slice:0')
        print res

if __name__ == '__main__':
    #'''
    cmd = 'killall chunkserver'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    time.sleep(3)
    cmd = 'cd /home/letian/banyandb/chunkserver_master/;bin/chunkserver&'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    time.sleep(1)
    cmd = 'cd /home/letian/banyandb/chunkserver_slave/;bin/chunkserver&'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    time.sleep(2)
    #'''
    unittest.main()
