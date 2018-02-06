## php
- 例子
php sdk 略有修改 增加了命名空间 看见没有带的例子 自行补全 BanyanDB 

- 新版本的 BanyanDB 的特性
    1.  新增强制读主
    2.  默认随机读写
    3.  新增可选reqeust id 跟踪 读写流程
    4.  新增vset 数据类型 vset($key, $member, $score, $val) 增加 value 字段
  
- 新版api 明确 namespace 和 table 的空间划分 不在自己拼key


``` php
$conf = array(
    "hosts" => array(
        "10.10.105.5:10100",
        "10.10.105.5:10200",
    ),
    "read_timeout_ms" => 3000,
    "max_request_retry" => 1,
    "retry_on_writes" => 1,
);
$cli = BanyanDB\BanyanDBCluster::GetBanyanClient($conf, "test", "api_test");
$res = $cli->set($key, $val);
$res = $cli->get($key);
var_dump($res);
```

banyandb支持的数据类型
===
## kv
- 数据结构
``` shell 
key1 value1
key2 value2
key3 value3
key4 value3
key5 value3
...
```
- cli的使用
``` shell 
[BanyanDB 10.10.105.5:10200]>>> set foo bar
[ok]
'set' succeed!
(1.144 ms)
[BanyanDB 10.10.105.5:10200]>>> get foo
[ok]
bar
(1.162 ms)
```

## hash
- 数据结构
``` shell 
key field1 value1
key field2 value2
key field3 value3
...
```
- cli的使用
``` shell 
[BanyanDB 10.10.105.5:10200]>>> hset xx name jack
[ok]
'hset' succeed!
(1.188 ms)
[BanyanDB 10.10.105.5:10200]>>> hget xx name
[ok]
jack
(0.958 ms)
```

## zset
- 数据结构
``` shell 
key member1 score1
key member2 score2
key member3 score3
...
```
- cli的使用
``` shell 
[BanyanDB 10.10.105.5:10200]>>> zset xx shuxue 100
[ok]
'zset' succeed!
(1.099 ms)
[BanyanDB 10.10.105.5:10200]>>> zget xx shuxue 
[ok]
100
(1.161 ms)
```

## vset
- 数据结构
``` shell 
key member1 score1 value1
key member2 score2 value2
key member3 score3 value3
...
```
- cli的使用
``` shell 
[BanyanDB 10.10.105.5:10200]>>> vset xx shuxue 100 abc
[ok]
'vset' succeed!
(1.173 ms)
[BanyanDB 10.10.105.5:10200]>>> vget xx shuxue
[ok]
 key             value          
------------------------------
 100             : abc            
1 result(s)
(1.079 ms)
```

## queue
- 数据结构
``` shell
key member1
key member2
key member3
...
```
- cli使用
``` shell 
[BanyanDB 10.10.105.5:10200]>>> qpush xx a b c
[ok]
'qpush' succeed!
(1.142 ms)
[BanyanDB 10.10.105.5:10200]>>> qpop xx 
[ok]
             key
----------------
               a
1 result(s)
(1.073 ms)
```

## jdelay
- 说明
定期执行语句
- cli使用
``` shell
[BanyanDB 10.10.105.5:10200]>>> set foo pre
[ok]
'set' succeed!
(0.377 ms)
[BanyanDB 10.10.105.5:10200]>>> jdelay set foo ok 3
[ok]
'jdelay' succeed!
(4.71 ms)
[BanyanDB 10.10.105.5:10200]>>> get foo
[ok]
pre
(0.984 ms)
[BanyanDB 10.10.105.5:10200]>>> get foo 
[ok]
pre
(2.031 ms)
```

banyandb sdk使用说明




===



## java
- 例子
``` java
ClusterLink clusterlink = new ClusterLink("10.10.105.5:10025", 1024);
BanyanDBClient cli = new BanyanDBClient(clusterlink, "test", "api_test");
resBool = cli.set(key, val);
resStr = cli.get(key);
System.out.println(resStr);
```

## python
- 例子
``` python
cli = BanyanClient(agent, 'test', 'api_test')
res = self.cli.request('set', [key, 'v0'])
self.assertTrue(res.is_ok == True and res.val == 1)
res = self.cli.request('get', [key])
self.assertTrue(res.is_ok == True and res.res[1] == 'v0')
```