import time
import os
import unittest
from banyan_api import BanyanClient


class TestBinlog(unittest.TestCase):
    def setUp(self):
        self.prefix = '%d_' % int(time.time())
        self.mck_dir = '/home/letian/banyandb/chunkserver_master/'
        self.sck_dir = '/home/letian/banyandb/chunkserver_slave/'
        cmd = 'killall chunkserver'
        status = os.system(cmd)
        print 'cmd:%s status:%d' % (cmd, status)
        time.sleep(3)

    def tearDown(self):
        cmd = 'killall chunkserver'
        status = os.system(cmd)
        print 'cmd:%s status:%d' % (cmd, status)

    def test_binlog(self):
        print "===test binlog sync(copy)==="
        print 'start master'
        cmd = 'cd %s;bin/chunkserver&' % (self.mck_dir)
        status = os.system(cmd)
        self.assertEqual(status, 0)
        time.sleep(1)
        ipport = ['127.0.0.1:10025']
        mcli = BanyanClient(ipport, 'test', 'api_test')
        for i in range(0, 20480):
            key = self.prefix + 'k%u' % i
            val = '%u' % i
            res = mcli.request('set', [key, val])
            self.assertEqual(res.is_ok, True)
        res = mcli.request('bseqget', [key], 'slice:0')
        self.assertTrue(res.is_ok == True and len(res.val) == 2)
        cmd = 'cd %s;bin/chunkserver&' % self.sck_dir
        status = os.system(cmd)
        self.assertEqual(status, 0)
        time.sleep(6)
        ipport = ['127.0.0.1:10026']
        scli = BanyanClient(ipport, 'test', 'api_test')
        print '%s slave start binlog sync' % time.ctime()
        key = self.prefix + 'k%u' % 8888
        while True:
            res = mcli.request('bseqget', [key], 'slice:0')
            self.assertTrue(res.is_ok == True and len(res.val) == 2)
            master_min_seq = int(res.val['min_seq'])
            master_max_seq = int(res.val['max_seq'])
            print 'master min seq %d master max seq %d' % (master_min_seq, master_max_seq)
            res = scli.request('bseqget', [key], 'slice:0')
            self.assertTrue(res.is_ok == True and len(res.val) == 2)
            slave_min_seq = int(res.val['min_seq'])
            slave_max_seq = int(res.val['max_seq'])
            print 'slave_min_seq %d slave max seq %d' % (slave_min_seq, slave_max_seq)
            #if (slave_min_seq == slave_max_seq and slave_max_seq >= master_max_seq):
            if (slave_max_seq >= master_max_seq):
                print 'master max seq %d, slave max seq %d' % (master_max_seq, slave_max_seq)
                break;
            else:
                time.sleep(5)
        print '%s slave complete binlog sync' % time.ctime()
        res = scli.request('get', [key])
        #print res
        self.assertTrue(res.is_ok == True and res.val == '8888')
        print "===test binlog sync(async)==="
        for i in range(0, 1024):
            key = self.prefix + 'kx%u' % i
            val = '%u' % i
            res = mcli.request('set', [key, val])
            self.assertEqual(res.is_ok, True)
        time.sleep(2)
        key = self.prefix + 'kx%u' % 888
        res = scli.request('get', [key])
        self.assertTrue(res.is_ok == True and res.val == '888')
        res = mcli.request('bseqget', [key], 'slice:0')
        self.assertTrue(res.is_ok == True and len(res.val) == 2)
        master_max_seq = int(res.val['max_seq'])
        self.assertTrue(master_max_seq != slave_max_seq)


if __name__ == '__main__':
    unittest.main()
