# -*- coding:utf-8 -*-

import os, sys, time
import unittest
import threading
import thread
from sys import stdin, stdout
from time import ctime, sleep
from banyan_api import BanyanClient


def test_kv_perf(seq):
    cnt = 100 * 1000
    start_seq = (seq - 1) * cnt
    end_seq = seq * cnt
    banyandb_agents = ['127.0.0.1:10600'] 
    bcli = BanyanClient(banyandb_agents)
    bcli.change_namespace('in_ns')
    bcli.change_table('in_table')
    print("-----start kv perf test-----\n")
    print("cnt:%d\n" %cnt)
    print("start time: %s" %ctime())
    start = time.time()
    for x in range(start_seq, end_seq):
        key = '%d' %x
        (bcli.request('hset', ['test_h0', "".join(['k', key]), key]))
    for x in range(start_seq, end_seq):
        key = '%d' %x
        (bcli.request('hget', ['test_h0', "".join(['k', key]) ] ))
    end = time.time()
    duration = end - start;
    qps = cnt * 2 / duration
    print("qps kv: time:%.3f qps:%.3f" %(duration, qps) )
    print("end time: %s\n" %ctime())
    print("-----end kv perf test-----\n")
    thread.exit_thread()

def test_zset_perf(seq):
    cnt = 10
    start_seq = (seq - 1) * cnt
    end_seq = seq * cnt
    banyandb_agents = ['127.0.0.1:10600'] 
    bcli = BanyanClient(banyandb_agents)
    bcli.change_namespace('in_ns')
    bcli.change_table('in_table')
    print("-----start zset perf test-----\n")
    print("cnt:%d\n" %cnt)
    print("start time: %s" %ctime())
    start = time.time()
    for x in range(start_seq, end_seq):
        key = '%d' %x
        print(bcli.request('zset', ['test_z0', "".join(['z', key]), key, x]))
    for x in range(start_seq, end_seq):
        key = '%d' %x
        print(bcli.request('zget', ['test_z0', "".join(['z', key]), key] ))
    end = time.time()
    duration = end - start;
    qps = cnt * 2 / duration
    print("qps kv: time:%.3f qps:%.3f" %(duration, qps) )
    print("end time: %s\n" %ctime())
    print("-----end zset perf test-----\n")
    thread.exit_thread()


def test_multi_kv_perf(seq):
    cnt = 1 * 1000
    start_seq = (seq - 1) * cnt
    end_seq = seq * cnt
    banyandb_agents = ['127.0.0.1:10600'] 
    bcli = BanyanClient(banyandb_agents)
    bcli.change_namespace('in_ns')
    bcli.change_table('in_table')
    print("-----start multi perf test-----\n")
    print("cnt:%d\n" %cnt)
    print("start time: %s" %ctime())
    start = time.time()
    for x in range(start_seq, end_seq):
        kv = []
        for y in range(x * 10, (x + 1) * 10):
            kv.append("".join(['k', '%d' %y]))
            kv.append("".join(['v', '%d' %y]))
        print(bcli.request('multi_set', kv))
    for x in range(start_seq, end_seq):
        key = []
        for y in range(x * 10, (x + 1) * 10):
            key.append("".join(['k', '%d' %y]))
        print(bcli.request('multi_get', key))
    end = time.time()
    duration = end - start;
    qps = cnt * 2 / duration
    print("qps multi: time:%.3f qps:%.3f" %(duration, qps) )
    print("end time: %s\n" %ctime())
    print("-----end multi perf test-----\n")
    thread.exit_thread()


def test_perf():
    for pid in range(1, 2):
        thread.start_new_thread(test_zset_perf, (pid,))

def test_multi_perf():
    for pid in range(1, 3):
        thread.start_new_thread(test_multi_kv_perf, (pid,))

if __name__ == '__main__':
    #test_perf()
    test_multi_perf()
    time.sleep(50 * 60)


