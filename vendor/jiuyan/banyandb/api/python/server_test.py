import time
import os
import unittest
from banyan_api import BanyanClient


if __name__ == '__main__':
    ipport = ['127.0.0.1:10024']
    acli = BanyanClient(ipport, 'test', 'api_test')
    ipport = ['127.0.0.1:10025']
    res = acli.request('info')
    print res
    res = acli.request('slice_info')
    print res
    scli = BanyanClient(ipport, 'test', 'api_test')
    res = scli.request('info')
    print res
    res = scli.request('leveldb')
    print res
    res = scli.request('dbsize')
    print res
    res = scli.request('slice_dbsize', [], 'slice:0')
    print res
    res = scli.request('dbrecord')
    print res

