import time
import os
import unittest
from banyan_api import BanyanClient


if __name__ == '__main__':
    ipport = ['127.0.0.1:10025']
    mcli = BanyanClient(ipport, 'test', 'api_test')
    res = mcli.request('delete_table')
    print res
    res = acli.request("keys", ["", "", "10"]);
    print res
    res = acli.request("rkeys", ["", "", "10"]);
    print res

