import os
import time
from banyan_api import BanyanClient

if __name__ == '__main__':
    agents = ['127.0.0.1:10024']
    cli = BanyanClient(agents, 'test', 'api_test')
    t = time.time()
    prefix = 'failover_%d_' % t
    key = prefix + 'k0'
    val = 'v0'
    res = cli.request('set', [key, val])
    print res
    cli.send_request('get', [key])
    cli.close()
