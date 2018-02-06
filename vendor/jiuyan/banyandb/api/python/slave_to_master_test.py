import time
import os
import unittest
from banyan_api import BanyanClient

class TestSlaveToMaster(unittest.TestCase):
    def setUp(self):
        t = time.time();
        self.prefix = 'slice_slave_to_master_test_%u_' % t

    def tearDown(self):
        pass

    def test_slice_slave_to_master(self):
        print "===test slice slave_to_master==="
        ns = 'test'
        table = 'api_test'
        agent_ipport = ['127.0.0.1:10024']
        master_ipport = ['127.0.0.1:10025']
        slave_ipport = ['127.0.0.1:10026']
        acli = BanyanClient(agent_ipport, ns, table)
        mcli = BanyanClient(master_ipport, ns, table)
        res = mcli.request('zk_slice_stop_writer', [], 'slice:0')
        self.assertEqual(res.is_ok, True)
        mcli = BanyanClient(master_ipport, ns, table)
        res = mcli.request('slice_info', [ns, table])
        print res.val
        key = self.prefix + "x0";
        val = "slave_to_master"
        res = acli.request("set", [key, val])
        self.assertEqual(res.is_buffer, True)
        key = self.prefix + "x1";
        res = acli.request("set", [key, val])
        self.assertEqual(res.is_buffer, True)

        scli = BanyanClient(slave_ipport, ns, table)
        res = scli.request('zk_slave_to_master', [], 'slice:0')
        self.assertEqual(res.is_ok, True)
        scli = BanyanClient(slave_ipport, ns, table)
        res = scli.request('slice_info', [ns, table])
        print res.val

if __name__ == '__main__':
    cmd = 'killall agent chunkserver'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    time.sleep(3)
    cmd = 'cd /home/letian/banyandb/agent0/;bin/agent&'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    time.sleep(1)
    cmd = 'cd /home/letian/banyandb/chunkserver_master/;bin/chunkserver&'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    time.sleep(1)
    cmd = 'cd /home/letian/banyandb/chunkserver_slave/;bin/chunkserver&'
    status = os.system(cmd)
    print 'cmd:%s status:%d' % (cmd, status)
    time.sleep(2)
    unittest.main()
