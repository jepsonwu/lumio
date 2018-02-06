import time
import os
import unittest
from banyan_api import BanyanClient


if __name__ == '__main__':
    ipport = ['127.0.0.1:10024']
    acli = BanyanClient(ipport, 'test', 'api_test')
    acli = BanyanClient(ipport, 'test', 'api_test')
    acli = BanyanClient(ipport, 'test', 'api_test')
    res = acli.request("keys", ["", "", "10"]);
    print res
    res = acli.request("rkeys", ["", "", "10"]);
    print res
    #res = acli.request("scan", ["", "", "10"]);
    #print res
    #res = acli.request("rscan", ["", "", "10"]);
    #print res
    res = acli.request("hkeys", ["agent_1460530410_hk5", "", "", "10"]);
    print res
    res = acli.request("hscan", ["agent_1460530410_hk5", "", "", "10"]);
    print res
    res = acli.request("hlist", ["", "", "10"]);
    print res
    res = acli.request("hrkeys", ["agent_1460530410_hk5", "", "", "10"]);
    print res
    res = acli.request("hrscan", ["agent_1460530410_hk5", "", "", "10"]);
    print res
    res = acli.request("hrlist", ["", "", "10"]);
    print res
    res = acli.request("zlist", ["", "", "10"]);
    print res
    res = acli.request("zrlist", ["", "", "10"]);
    print res
    res = acli.request("qlist", ["", "", "10"]);
    print res
    res = acli.request("qrlist", ["", "", "10"]);
    print res
    res = acli.request("jllist", ["", "", "10"]);
    print res
    #ipport = ['127.0.0.1:10025']
