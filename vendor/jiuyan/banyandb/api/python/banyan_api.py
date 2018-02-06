import os
import sys
import socket
import random
import uuid
import collections


class BanyanClient(object):

    #_NO_RETRY_CMD = set(['incr', 'decr', 'hincr', 'hdecr', 'zincr', 'zdecr'])
    _NO_RETRY_CMD = set([])

    def __init__(self, hosts, ns, tab, timeout = 3, retries = 3):
        self.hosts = hosts
        self.ns = ns
        self.tab = tab
        self.delay_ns = 'banyan'
        self.delay_tab = 'delay'
        self.proto = 'by'
        self.read_buf = ''
        #self.host_index = random.randint(0, len(hosts))
        self.sock = None
        self.retries = retries
        self.timeout = timeout
        self.fail_count = 0
        self.cmd = ''
        self.connection()
        if self.sock == None:
            print("connection failed")

    def change_namespace(self, ns):
        self.ns = ns

    def change_table(self, tab):
        self.tab = tab

    def connection(self):
        if self.fail_count > 0:
            self.fail_count = 0
        if self.sock != None:
            self.close()
        nhost = len(self.hosts)
        index = random.randint(0, nhost)
        for i in range (index, nhost):
            (host, port) = tuple(self.hosts[i].split(':'))
            #print("connect hosts[%d] %s:%s" % (i, host, port))
            try:
                self.sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                self.sock.settimeout(self.timeout)
                self.sock.connect((host, int(port)))
                self.sock.settimeout(None)
                self.sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1)
                self.fail_count = 0
                return
            except Exception, e:
                print("connect hosts[%d] %s:%s failed:%s" % (i, host, port, e))
                self.close()
                pass
        for i in range (0, index):
            (host, port) = tuple(self.hosts[i].split(':'))
            #print("connect hosts[%d] %s:%s" % (i, host, port))
            try:
                self.sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                self.sock.settimeout(self.timeout)
                self.sock.connect((host, int(port)))
                self.sock.settimeout(None)
                self.sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1)
                self.fail_count = 0
                return
            except Exception, e:
                print("connect hosts[%d] %s:%s failed:%s" % (i, host, port, e))
                self.close()
                pass
        raise "No valid service"

    def close(self):
        if self.sock != None:
            self.sock.close()
            self.sock = None

    def request(self, cmd, params = None, options = None):
        reqs, opts = self.package_request(cmd, params, options)
        write_buf = self.serizlize_request(reqs)
        self.send_buf(write_buf)
        res = self.recv_response()
        return BanyanResponse(cmd, params, opts, res)

    def send_request(self, cmd, params = None, options = None):
        reqs, opts = self.package_request(cmd, params, options)
        write_buf = self.serizlize_request(reqs)
        self.send_buf(write_buf)

    def package_request(self, cmd, params = None, options = None):
        uid = 'py_%s' % uuid.uuid1()
        #print uid
        send_param = []
        if params != None:  send_param = params
        if options != None:
            opts = 'ns:{0},tab:{1},proto:{2},rid:{3},{4}'.format(self.ns, self.tab, self.proto, uid, options)
        else:
            opts = 'ns:{0},tab:{1},proto:{2},rid:{3}'.format(self.ns, self.tab, self.proto, uid)
        if cmd == 'mhmget' and send_param != []:
            opts = "{0},nkey:{1},nfield:{2}".format(opts, len(send_param[0]), len(send_param[1]))
            send_param = send_param[0] + send_param[1]
        elif cmd == 'jdelay' and send_param != []:
            opts2 = opts
            if options != None:
                opts = 'ns:{0},tab:{1},proto:{2},rid:{3},{4}'.format(self.delay_ns, self.delay_tab, self.proto, uid, options)
            else:
                opts = 'ns:{0},tab:{1},proto:{2},rid:{3}'.format(self.delay_ns, self.delay_tab, self.proto, uid)
            reqs2 = [opts2] + send_param[:(len(send_param) - 1)]
            statements = self.serizlize_request(reqs2)
            send_param = [statements] + [send_param[len(send_param) - 1]]
        elif (cmd == 'jget' or cmd == 'jcancel' or cmd == 'jscan' or cmd == 'jclear') and send_param != []:
            if options != None:
                opts = 'ns:{0},tab:{1},proto:{2},rid:{3},{4}'.format(self.delay_ns, self.delay_tab, self.proto, uid, options)
            else:
                opts = 'ns:{0},tab:{1},proto:{2},rid:{3}'.format(self.delay_ns, self.delay_tab, self.proto, uid)
        reqs = [opts, cmd] + send_param
        self.cmd = cmd
        return reqs, opts

    def serizlize_request(self, reqs):
        params = []
        for m in reqs:
            params.append(str(len(m)))
            params.append(m)         
        sep = '\n'
        buf = sep.join(params) + '\n\n'
        return buf

    def send_buf(self, buf):
        if self.cmd_can_retry(self.cmd):
            while self.fail_count < self.retries:
                if self.tcp_send(buf):
                    return True
                else:
                    self.fail_count += 1
                    self.connection()
                    if self.sock == None:
                        print("Retry send failed")
        else:
            if self.tcp_send(buf):
                return True
            else:
                self.fail_count += 1
                self.connection()
                if self.sock == None:
                   print("Send failed (no retry)")
                return False
        return False

    def tcp_send(self, buf):
        try:
            while True:
                if self.sock == None:
                    print("None sock")       
                    return False
                ret = self.sock.send(buf)
                if ret == 0:
                    return False 
                buf = buf[ret : ]
                if len(buf) == 0:
                    break;
        except socket.error, e:
            return False
        return True 

    def cmd_can_retry(self, cmd):
        if cmd in self._NO_RETRY_CMD:
            return False
        return True

    def recv_response(self):
        while True:
            res = self.parse()
            if res == None:
                if self.tcp_recv() == 0:
                    return []
            else:
                return res

    def tcp_recv(self):
        try:
            d = self.sock.recv(1024 * 1024)           
        except Exception, e:
            d = ''
        if d == '':
            self.close()
            return 0
        self.read_buf += d;
        return len(d)

    def parse(self):
        ret = []
        spos = 0
        epos = 0
        while True:
            spos = epos
            epos = self.read_buf.find('\n', spos)
            if epos == -1:
                break
            epos += 1
            line = self.read_buf[spos : epos]
            spos = epos
            if line.strip() == '':
                if len(ret) == 0:
                    continue
                else:
                    self.read_buf = self.read_buf[spos : ]
                    return ret
            try:
                num = int(line)
            except Exception, e:
                return []
            epos = (spos + num)
            if epos > len(self.read_buf):
                break
            data = self.read_buf[spos : epos]
            ret.append(data)
            spos = epos
            epos = self.read_buf.find('\n', spos)
            if epos == -1:
               break
            epos += 1
        return None

class BanyanResponse(object):

    _BANYAN_RESPONSE_OK = 'ok'
    _BANYAN_RESPONSE_NOT_FOUND = 'not_found'
    _BANYAN_RESPONSE_BUFFER = 'buffer'
    _BANYAN_RESPONSE_ERROR = 'error'
    _CMD_RETURN_NONE = set(['jcancel', 'jclear', 'ping', 'quit', 'qset'])
    _CMD_RETURN_INT = set(['set', 'del', 'setx', 'expire', 'incr', 'decr', 'exists', 'getbit', 'setbit', 'jdelay', 'ttl', 'strlen', 'setnx']
                    + ['hset', 'hdel', 'hsize', 'hincr', 'hdecr', 'hexists', 'hclear', 'multi_hset', 'multi_hdel']
                    + ['zset', 'zdel', 'zsize', 'zincr', 'zdecr', 'zcount', 'zclear', 'zremrangebyrank', 'zexists', 'zsum', 'zrank', 'zrrank', 'zremrangebyscore']
                    + ['vset', 'vdel', 'vsize', 'vincr', 'vdecr', 'vcount', 'vclear', 'vremrangebyrank', 'vexists', 'vsum', 'vrank', 'vrrank', 'vremrangebyscore']
                    + ['multi_zset', 'multi_zdel']
                    + ['multi_vset', 'multi_vdel']
                    + ['qsize', 'qpush', 'qpush_back', 'qclear', 'qpush_front', 'qtrim_front', 'qtrim_back']
                    + ['jllen', 'jlpush', 'jlsetd', 'jldeld', 'jldel_all'])
    _CMD_RETURN_BIN = set(['get', 'getset', 'hget', 'hset_if_eq', 'hdel_if_eq', 'zget', 'jget', 'jlpop', 'jlgetd']
                    + ['substr', 'qfront', 'qback', 'qget']
                    + ['zk_task_get', 'zk_ns_table_add', 'zk_slice_list', 'zk_slice_list_chunk', 'zk_node_get']
                    + ['zk_slice_add_master', 'zk_slice_add_slave', 'zk_slice_find_key', 'zk_slice_split', 'zk_slave_to_master', 'zk_slice_drop', 'zk_slice_delete', 'zk_ns_table_delete'])
    _CMD_RETURN_FLOAT = set(['zavg', 'vavg'])
    _CMD_RETURN_LIST = set(['scan', 'rscan', 'keys', 'rkeys']
                    + ['hkeys', 'hrkeys', 'hlist', 'hrlist', 'hgetall', 'hscan', 'hrscan']
                    + ['zscan', 'zrscan', 'zrange', 'zrrange', 'zlist', 'zrlist', 'zkeys']
                    + ['vscan', 'vrscan', 'vrange', 'vrrange', 'vlist', 'vrlist', 'vkeys']
                    + ['vget', 'multi_vget']
                    + ['qpop', 'qpop_front', 'qslice', 'qrange', 'jlslice', 'jllist', 'qpop_back']
                    + ['info', 'slice_info', 'leveldb', 'cmdinfo', 'delete_table', 'dbscan'])
    _CMD_RETURN_INT_LIST = set(['multi_set', 'multi_del', 'jscan'])
    _CMD_RETURN_MAP = set(['multi_get', 'multi_hget', 'multi_zget', 'bseqget', 'dbsize', 'dbrecord', 'slice_dbsize'])
    _CMD_RETURN_TABLE  = set(['mhmget'])

    def __init__(self, cmd, params, options, res):
        self.cmd = cmd
        self.params = params
        self.options = options
        self.res = res
        self.val = None
        self.val_type = 'list'
        self.is_ok = False
        self.is_not_found = False
        self.is_buffer = False
        self.is_error = True
        self.parse_response()

    def __repr__(self):
        return '[{0}]{1}({2})==>({3}:{4}){5}'.format(str(self.options), str(self.cmd), str(self.params), str(self.val_type), str(self.val), str(self.res))

    def parse_response(self):
        self.val = None
        if self.res == None or len(self.res) == 0:
            self.is_ok = False
            self.is_not_found = False
            self.is_buffer = False
            self.is_error = True
        else:
            if self.res[0] == self._BANYAN_RESPONSE_OK:
                self.is_ok = True
                self.is_not_found = False
                self.is_buffer = False
                self.is_error = False
                self.parse_ok_response()
            elif self.res[0] == self._BANYAN_RESPONSE_NOT_FOUND:
                self.is_ok = False 
                self.is_not_found = True 
                self.is_buffer = False
                self.is_error = False
            elif self.res[0] == self._BANYAN_RESPONSE_BUFFER:
                self.is_ok = False
                self.is_not_found = False 
                self.is_buffer = True 
                self.is_error = False
            else:
                self.is_ok = False 
                self.is_not_found = False 
                self.is_buffer = False
                self.is_error = True
                if (self.cmd == 'hset_if_eq' or self.cmd == 'hdel_if_eq') and len(self.res) > 1:
                    self.val = self.res[1]

    def parse_ok_response(self):
        if self.cmd in self._CMD_RETURN_NONE:
            self.val = None
            self.val_type = 'None'
        elif self.cmd in self._CMD_RETURN_BIN:
            #if len(self.res) != 2:
            #    raise "wrong number of res items"
            self.val = self.res[1]
            self.val_type = 'bin'
        elif self.cmd in self._CMD_RETURN_INT:
            self.val = int(self.res[1])
            self.val_type = 'int'
        elif self.cmd in self._CMD_RETURN_FLOAT:
            self.val = float(self.res[1])
            self.val_type = 'float'
        elif self.cmd in self._CMD_RETURN_LIST:
            self.val = self.res[1:]
            self.val_type = 'list'
        elif self.cmd in self._CMD_RETURN_INT_LIST:
            self.val = int(self.res[1])
            self.val_type = 'int_list'
        elif self.cmd in self._CMD_RETURN_MAP:
            self.val = collections.OrderedDict()
            n = len(self.res)
            if n % 2 == 0:
                raise "wrong number of res items"
            for i in range(1, n, 2):
                self.val[self.res[i]] = self.res[i + 1]
            self.val_type = "map"
        elif self.cmd in self._CMD_RETURN_TABLE:
            self.val = {}
            n = len(self.res)
            nkey = len(self.params[0])
            nfield = len(self.params[1])
            #print self.res
            if n != (3 * nkey * nfield + 1):
                raise "wrong number of res iterms"
            for i in range(1, n, 3):
                key = self.res[i]
                field = self.res[i + 1]
                val = self.res[i + 2]
                items = {}
                if self.val.has_key(key):
                     items = self.val[key]     
                items[field] = val
                self.val[key] = items
            self.val_type = "table"
        else:
            self.val = self.res[1:]
            self.val_type = "listd"

    def ok(self):
        return self.is_ok
    
    def not_found(self):
        return self.is_not_found
    
    def error(self):
        return self.is_error

    def val(self):
        return self.val
