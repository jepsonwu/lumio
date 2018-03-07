# thrift client

## 功能
1. 封装统一调用方式
2. 重试处理
3. rpc异常处理
4. 日志，监控, 缓存统一处理
5. 服务器的负载处理

## laravel 框架系的使用者，可以参考`ThrfitServiceProvider.php`这个文件

## 使用要求

1. 安装`make`命令, mac自带，windows需要自行安装
2. 安装`composer`命令 [http://docs.phpcomposer.com/03-cli.html#create-project]
3. 安装`thrift`命令 [https://thrift.apache.org/docs/install/]

4. 安装thrift_client包
```
"require": {
    "cuckoo/thrift_client":"1.0.6"
}
```

5. 创建thrift/packages文件目录，用于生成约定文件，IN项目创建的地址为:
```
/lib/packages/thrift/packages
```

6. 在composer.json中使用classmap加载文件，in项目中可能需要使用相对路径
```
"classmap": [
    "/lib/packages/thrift/packages"
]
```

7. 把需要的thrift文件放置在第四步创建的thrift目录中, 并在该目录创建Makefile文件用于自动编译创建thrift对应的目标文件
Makefile 文件编写参考
```
build:
    @echo "\n--------------> build thrift <--------------\n"
    rm -rf packages/*
    thrift -nowarn --gen php:server -out packages/ sms.thrift
```

技巧提示: 可以在项目根目录下再创建一个Makefile文件, 这样，以后可以直接在根目录下执行`make build`
```
build:
    cd 第四步安装的目录 && $(MAKE) build
    composer dump-autoload
```
完成之后，执行`make build` 生成thrift文件

8, 创建配置文件，配置定义
```
$config = array(
    # promo_sms 是服务名
    'promo_sms' => array(
        'client' => 'SMS\MessageServiceClient', # thrift生成的文件的类名
        'hosts' => ['10.10.106.28'], # 服务器
        'port' => 8092, # 端口
        'persist' => false, # 是否使用持久连接
        'receive_timeout' => 2000, # 超时
        'send_timeout' => 1000, # 超时
        'read_buf_size' => 1024, # socket read buff size
        'write_buf_size' => 1024, # socket write buff size
        'host_picker' => 'null' # 可执行的函数名，用于获取hosts
        'transport' => 'TBufferedTransport', # 支持的传输协议，可选择【TBufferedTransport,TFramedTransport 】
        'tracked' => 是否接入链路跟踪系统
        'binary' => 'jiuyan' //如果配置jiuyan 则使用九言定制的协议
    )
);
```

## 使用姿势参考
```
use Jiuyan\Cuckoo\ThriftClient\ThriftDao;
use Jiuyan\Cuckoo\ThriftClient\ClientFactory;
use Jiuyan\Cuckoo\ThriftClient\Manager;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require dirname(__FILE__) . '/../vendor/autoload.php';

$config = include dirname(__FILE__) . '/../config/config.php';

$factory = new ClientFactory();

$manager = new Manager($factory, $config);

$dao = new ThriftDao();

$dao->setManager($manager);

// local是服务名
$data = $dao->service('local')->call('ping')->run();
echo "response data: \n";
print_r($data);```

# API
1. `retry(int)` 重试次数
```
$dao->service('promo_sms')->call('send')->with($params)->retry(3)->run();
```

2. `cached(int)` 缓存时间,单位`s`, 需要配合`cachedData`使用
```
$dao->service('sms')->call('send')->with($params)->cached(60)->cachedData();
```

3. `result(default)`获取返回结果，忽略所有异常, 有异常则返回`default`
```
$dao->service('sms')->call('send')->with($params)->result(1);
```

4. `data(default)`,获取返回结果，忽略`UserException`异常, 有异常则返回`default`
```
$dao->service('sms')->call('send')->with($params)->data(1);
```

5. `execute()`,获取返回结果，忽略所有异常, 有异常则返回`false`, 没有异常返回`true`
```
$dao->service('sms')->call('send')->with($params)->execute(1);
```
6. `call(method)`， 指定需要调用的method

7. `with`, 指定调用method 需要传递的参数

8. `service`, 指定服务名，这个服务名，就是配置文件中服务名


## 异常处理
thrift 中定义了两类异常:
```
exception UserException {
    1: required SMSErrorCode error_code,
    2: required string error_name,
    3: optional string message,
}

exception SystemException {
    1: required SMSErrorCode error_code,
    2: required string error_name,
    3: optional string message,
}
```

thrift server可能会抛出这两类一次， 不过thrift client已经捕获了thrift定义的异常，重新抛出了client自定义
的异常，详情可以参考`UserException.php, ServerException.php` 两个文件

thrift client使用者需要根据自身的需要，处理这两个异常`UserException.php, ServerException.php`


## 短信服务配置

1. 端口: 8092
2. QA: 10.10.106.28
3. webtest: 58.215.141.112
4. 线上营销短信服务: 192.168.1.194 192.168.1.214 192.168.1.193 192.168.1.93 192.168.1.45
4. 线上验证码短信服务: 192.168.1.172 192.168.1.55 192.168.1.84 192.168.1.56 192.168.1.57
