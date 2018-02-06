import time
import json
import unittest
from banyan_api import BanyanClient


cli = None

def check_task_status(self, task_id, sleep_time):
    time.sleep(sleep_time)
    res = self.cli.request('zk_task_get', [task_id])
    task = json.loads(res.val)
    status = task["main_task"]["status"]
    self.assertTrue(res.is_ok == True and status == 'ok', res.val)

class TestCluster(unittest.TestCase):
    def setUp(self):
        t = time.time();
        self.prefix = 'cluster_%u_' % t
        #self.ns = prefix + 'ns'
        #self.table = prefix + 'table'
        self.cli = cli

    def tearDown(self):
        pass

    def test_xx(self):
        print "===test zk_ns_table_add==="
        ns = self.prefix + 'ns'
        table = self.prefix + 'table'
        res = self.cli.request('zk_ns_table_add', [ns, table])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)

        print "===test zk_slice_add_master==="
        add_master = "10.10.105.5:10025"
        res = self.cli.request('zk_slice_add_master', [ns, table, add_master])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)
        acli = BanyanClient(['10.10.105.5:10024'], ns, table)
        key = self.prefix + 'k0'
        val = 'v0'
        res = acli.request('set', [key, val])
        self.assertTrue(res.is_ok == True and res.val == 1)

        print "===test zk_slice_add_slave==="
        add_slave = "10.10.105.5:10026"
        slice_id = '1'
        res = self.cli.request('zk_slice_add_slave', [ns, table, slice_id, add_slave])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)
        scli = BanyanClient([add_slave], ns, table)
        res = scli.request('get', [key])
        self.assertTrue(res.is_ok == True and res.val == val)

        print "===test zk_slave_to_master==="
        new_master = "10.10.105.5:10026"
        slice_id = '1'
        res = self.cli.request('zk_slave_to_master', [ns, table, slice_id, new_master])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)
        key = self.prefix + 'k1'
        val = 'v1'
        res = acli.request('set', [key, val])
        self.assertTrue(res.is_ok == True and res.val == 1)
        print "===wait binlog compelte==="
        time.sleep(2)
        new_mcli = BanyanClient([new_master], ns, table)
        res = new_mcli.request('get', [key])
        self.assertTrue(res.is_ok == True and res.val == val, res)
        new_scli = BanyanClient([add_master], ns, table)
        res = new_scli.request('get', [key])
        self.assertTrue(res.is_ok == True and res.val == val)

        print "===test zk_slice_split==="
        master = "10.10.105.5:10026"
        slave = "10.10.105.5:10025"
        slice_id = '1'
        # split is k (h k z)
        res = self.cli.request('zk_slice_split', [ns, table, slice_id, 'k'])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)
        mcli = BanyanClient([master], ns, table)
        key = self.prefix + 'h0'
        field = 'f0'
        val = 'v0'
        res = mcli.request('hset', [key, field, val])
        self.assertTrue(res.is_ok == True and res.val == 1)
        key = self.prefix + 'z0'
        member = 'm0'
        val = '8'
        res = mcli.request('zset', [key, member, val])
        self.assertTrue(res.is_ok == True and res.val == 1)
        
        print "===wait binlog complete==="
        time.sleep(2)
        res1 = mcli.request('bseqget', [], 'slice:2')
        print res1
        res2 = mcli.request('bseqget', [], 'slice:3')
        print res2

        scli = BanyanClient([slave], ns, table)
        res = scli.request('bseqget', [], 'slice:2')
        self.assertTrue(res.is_ok == True and res.val == res1.val)
        #print res
        res = scli.request('bseqget', [], 'slice:3')
        self.assertTrue(res.is_ok == True and res.val == res2.val)
        #print res

        ns = self.prefix + 'delete'
        table = self.prefix + 'delete_test'
        res = self.cli.request('zk_ns_table_add', [ns, table])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)

        add_master = "10.10.105.5:10025"
        res = self.cli.request('zk_slice_add_master', [ns, table, add_master])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)

        add_slave = "10.10.105.5:10026"
        slice_id = '1'
        res = self.cli.request('zk_slice_add_slave', [ns, table, slice_id, add_slave])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)

        acli = BanyanClient(["10.10.105.5:10024"], ns, table)
        for i in range(0,9):
            key = self.prefix + 'h' + str(i)
            field = 'f0'
            val = 'v0'
            res = acli.request('hset', [key, field, val])
            self.assertTrue(res.is_ok == True and res.val == 1)
        time.sleep(1)

        res = self.cli.request('zk_ns_table_delete', [ns, table])
        self.assertTrue(res.is_error == True)

        res = self.cli.request('zk_ns_table_delete', [ns])
        self.assertTrue(res.is_error == True)

        print "===test zk slice delete==="
        res = self.cli.request('zk_slice_delete', [ns, table, slice_id])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)

        print "===test zk slice drop==="
        res = self.cli.request('zk_slice_drop', [ns, table, slice_id, add_slave])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)

        res = self.cli.request('zk_slice_drop', [ns, table, slice_id, add_master])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)

        print "===test zk ns_table_delete==="
        res = self.cli.request('zk_ns_table_delete', [ns, table])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)

        res = self.cli.request('zk_ns_table_delete', [ns])
        taskid = res.val.split()[1]
        self.assertTrue(res.is_ok == True and taskid.isdigit() == True)
        check_task_status(self, taskid, 3)

if __name__ == '__main__':
    cli = BanyanClient(['10.10.105.5:1090'], 'test', 'api_test')
    unittest.main()
