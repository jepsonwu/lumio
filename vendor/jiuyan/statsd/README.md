# 数据监控设想
当我们做监控的时候，需要考虑几个关键字:

1. 资源(Resources)
系统中所有的功能组件(业务API，RPC调用，系统资源，网络等)

2. 资源利用率(Resource Utilization)
资源的忙绿时间比率

3. 任务饱和度(Saturation)
暂时还没获得资源的任务量

4. 吞吐量，出错量，响应时间
一个资源在处理的时，吞吐量，响应时间和出错量

这样，当一个API出错或者响应时间很慢，我们可以通过监控图表，比较准确的定位到问题。

进而，有针对性的对一个服务进行优化。

# 监控流程

`打点 —-> 搜集 —-> 聚合 —->储存 —->展示`


1. 打点: statsd-php 作为client 搜集数据
2. 搜集和聚合: statsd  接受client传递过来的数据， 对数据进行聚合处理
3. 储存: influxdb 一个时序数据库, 储存statsd传递的数据
4. 展示: grafana

# statsd 协议
statsd采用简单的行协议：
```
<bucket>:<value>|<type>[|@sample_rate]
```

`bucket`

bucket是一个metric的标识，可以看成一个metric的变量。

`value`

metric的值，通常是数字。

`type`

metric的类型，通常有timer、counter、gauge和set四种。

`sample_rate`

如果数据上报量过大，很容易溢满statsd。所以适当的降低采样，减少server负载。
比如客户端1s内向statsd发送10次请求，可能会溢满statsd, 这时可以设置一个sample_rate = 0.1,
客户端要做的事情是1s内只发送一次请求(客户端需要计算，只有请求次数等于10的时候才发送请求)，statsd服务器按采样频率恢复数据来发送给backend(influxdb)， 1/0.1 = 10



# Metrics 类型
`Counting`
counter类型的指标，用来计数。在一个flush区间，把上报的值累加。值可以是正数或者负数。

```
user.logins:10|c        // user.logins + 10
user.logins:-1|c        // user.logins - 1
user.logins:10|c|@0.1   // user.logins + 100

```
`Gauges`

gauge是任意的一维标量值。gague值不会像其它类型会在flush的时候清零，而是保持原有值。statsd只会将flush区间内最后一个值发到后端。另外，如果数值前加符号，会与前一个值累加。

可以用于统计CPU，网络IO等系统资源

```
age:10|g    // age 为 10
age:+1|g    // age 为 10 + 1 = 11
age:-1|g    // age为 11 - 1 = 10
age:5|g     // age为5,替代前一个值

```

`timer`
timers用来记录一个操作的耗时，单位ms。statsd会记录平均值（mean）、最大值（upper）、最小值（lower）、累加值（sum）、平方和（sum_squares）、个数（count）以及部分百分值。

```
stats.apps.middle.message.aciton.send:100|ms
```
statsd 接收到`timer`类型的数据，会对数据进行聚合,
```
tag4=timer,tag5=mean_95 value=1
tag4=timer,tag5=upper_95 value=1
tag4=timer,tag5=sum_95 value=1
tag4=timer,tag5=sum_squares_95 value=1
tag4=timer,tag5=std value=0
tag4=timer,tag5=upper value=1   # 采样时间内的最大值
tag4=timer,tag5=lower value=1   # 采样时间内的最小值
tag4=timer,tag5=count value=1  # 采样时间内的请求数，如果采样时间和是1s，那么count = count_ps
tag4=timer,tag5=count_ps value=1  # 采样时间内，1s内请求数
tag4=timer,tag5=sum value=1
tag4=timer,tag5=sum_squares value=1
tag4=timer,tag5=mean value=1
tag4=timer,tag5=median value=1
```

对于百分数相关的数据需要解释一下。以90为例。statsd会把一个flush期间上报的数据，去掉10%的峰值，即按大小取cnt*90%（四舍五入）个值来计算百分值。
举例说明，假如10s内上报以下10个值。

```
1,3,5,7,13,9,11,2,4,8
```
则只取10*90%=9个值，则去掉13。百分值即按剩下的9个值来计算。

```
$KEY.mean_90   // (1+3+5+7+9+2+11+4+8)/9
$KEY.upper_90  // 11
$KEY.lower_90  // 1
````

# sets
记录flush期间，不重复的值。

```
request:1|s  // user 1
request:2|s  // user1 user2
request:1|s  // user1 user2
```

# 客户端 statsd-php

php版的([statsd](https://github.com/etsy/statsd))客户端, 使用此版本的客户端，会忽略所有的错误，防止statsd-php 错误是应用程序奔溃


## 安装
通过 `composer.json` 文件安装:

```javascript
{
    "require": {
        "Jiuyan/statsd": "~2.0"
    }
}
```

## 使用姿势
```php
<?php
$timeout = 1;
$connection = new \Domnikl\Statsd\Connection\UdpSocket('localhost', 8125, $timeout);
$statsd = new \Domnikl\Statsd\Client($connection, "stats.apps.middle");

// Counting
$statsd->increment("action.send");
$statsd->decrement("error.send");
$statsd->count("error.send", 1000);
```

### [Timings](https://github.com/etsy/statsd/blob/master/docs/metric_types.md#timing)

```php
<?php
// timings
$statsd->timing("foo.bar", 320);
$statsd->time("foo.bar.bla", function() {
    // code to be measured goes here ...
});

// more complex timings can be handled with startTiming() and endTiming()
$statsd->startTiming("foo.bar");
// more complex code here ...
$statsd->endTiming("foo.bar");
```


### [Gauges](https://github.com/etsy/statsd/blob/master/docs/metric_types.md#gauges)

```
<?php
// Absolute value
$statsd->gauge('foobar', 3);

// Pass delta values as a string.
// Accepts both positive (+11) and negative (-4) delta values.
$statsd->gauge('foobar', '+11');
```

### [Sets](https://github.com/etsy/statsd/blob/master/docs/metric_types.md#sets)

```
<?php
$statsd->set('userId', 1234);
```

# statsd batch send
API请求中，需要多次打点，可以使用batch
```
$statsd->startBatch();
$statsd->timing("foo.bar", 320);
$statsd->increment("foo.bar");
$statsd->endBatch();
```

# 额外扩展
https://github.com/etsy/statsd/wiki
