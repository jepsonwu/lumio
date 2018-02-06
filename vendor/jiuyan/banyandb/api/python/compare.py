# encoding=utf-8

import os, sys, time
import unittest
import hashlib
from sys import stdin, stdout
from banyan_api import BanyanClient

banyandb_agents = ['127.0.0.1:10100'] 
ssdb_server = ['127.0.0.1:10800']
bcli = BanyanClient(banyandb_agents)
bcli.change_namespace('in_device')
bcli.change_table('device')
ssdbcli = BanyanClient(ssdb_server)
ssdbcli.change_namespace('in_device')
ssdbcli.change_table('device')

#for x in range(0, 10000):

assert str(bcli.request('set', ['test_k1', 'test_v1'])) == str(ssdbcli.request('set', ['test_k1', 'test_v1'])), "wrong: set"
assert str(bcli.request('get', ['test_k1'])) == str(ssdbcli.request('get', ['test_k1'])), "wrong: get"

assert str(bcli.request('set', ['test_k2', 'test_v2'])) == str(ssdbcli.request('set', ['test_k2', 'test_v2'])), "wrong: set"

#assert str(bcli.request('multi_get', ['test_k1', 'test_k2'])) == str(ssdbcli.request('multi_get', ['test_k1', 'test_k2'])), "wrong: multi_get"

print(bcli.request('hset', ['test_h0', 'k0', 'v00']))
print(ssdbcli.request('hset', ['test_h0', 'k0', 'v00']))

print(bcli.request('hget', ['test_h0', 'k0']))
print(ssdbcli.request('hget', ['test_h0', 'k0']))

print(bcli.request('hset', ['test_h0', 'k1', 'v01']))
print(ssdbcli.request('hset', ['test_h0', 'k1', 'v01']))

print(bcli.request('multi_hget', ['test_h0', 'k0', 'k1']))
print(ssdbcli.request('multi_hget', ['test_h0', 'k0', 'k1']))

print(bcli.request('hgetall', ['test_h0']))
print(ssdbcli.request('hgetall', ['test_h0']))

print(bcli.request('hkeys', ['test_h0', "", "", 10]))
print(ssdbcli.request('hkeys', ['test_h0', "", "", 10]))

print(bcli.request('hscan', ['test_h0', "", "", 10]))
print(ssdbcli.request('hscan', ['test_h0', "", "", 10]))

print(bcli.request('hlist', ["", "", 10]))
print(ssdbcli.request('hlist', ["", "", 10]))

print(bcli.request('hdel', ['test_h0', 'k0']))
print(ssdbcli.request('hdel', ['test_h0', 'k0']))

print(bcli.request('hclear', ['test_h0', "", ""]))
print(ssdbcli.request('hclear', ['test_h0', "", ""]))


time.sleep(1)
