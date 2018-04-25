
# Lumen Framework



基于lumen定制的框架，引用了`l5-repository`, `dingo`,  `laravel-modules`, `profiler`, `auth`等模块。

可参考 `composer.json`文件.

PHP版本要求7.0.* 演示分支 demo  开发分支develop

## 安装
    

## 配置

执行 `php artisan vendor:publish` 可生成配置文件， 配置目录在 `project/config`.

执行之后会生成 `database.php`, `nosql`, `statsd.php`, `rpc.php`, `log.php`等配置

请自行查看需要配置的内容。


## 生成Module
此框架是基于模块开发的，首先必须生成模块。

“模块”功能 引用的是`https://github.com/nWidart/laravel-modules`

执行 `php artisan module:make ModuleName1, ModuleName2` 可以生成对应的模块，模块的相关操作请阅读

模块的相关配置在`project/config/module.php`中，注意必须先执行`php artisan vendor:publish`

才可以生成配置。

常用操作：

查看模块列表 `php artisan module:list`

设置模块有效 `php artisan module:enable Blog`

设置模块无效 `php artisan module:disenable Blog`

发布模块文档  `php artisan module:publish-config Blog`

更多信息请查看: `https://nwidart.com/laravel-modules/v2/advanced-tools/artisan-commands`


## 生成Repository

模块生成之后，需要在模块下创建Repository， 执行命令 `php artisan make:entity name --module=ModullName`

必须指定Modulename， 否则将在`project/app`下创建。

此模块引用 `andersao/l5-repository`. 提供访问数据库API的封装。


引用了Repository之后，我们的框架的程序将分为如下几层

```
Controller
    -- SomeBusinessController
        一个控制器内只能调用一个service , 其他业务逻辑在service内部完成
Service
    -- InternalService 
    -- RpcService
    -- SomeBusinessService
        每个serviceApi 格式化结果
Repository
    -- 支持格式化返回
Model
```

业务书写的基本原则：

controller: 只负责暴露API， 不做任何业务逻辑处理

Service: 处理业务，但是不能进行数据库查询操作

     -- InternalService 负责模块间 共享api
     -- RpcService 外部RPC API调用
     -- OtherService 业务API 
     
Repository: 负责数据库查询操作,调用Repositroy提供的API, 不能调用model方法

Model: 定义数据资源的属性

Repositroy常用操作：

```
all($columns = array('*'))
first($columns = array('*'))
paginate($limit = null, $columns = ['*'])
find($id, $columns = ['*'])
findByField($field, $value, $columns = ['*'])
findWhere(array $where, $columns = ['*'])
findWhereIn($field, array $where, $columns = [*])
findWhereNotIn($field, array $where, $columns = [*])
create(array $attributes)
update(array $attributes, $id)
updateOrCreate(array $attributes, array $values = [])
delete($id)
orderBy($column, $direction = 'asc');
with(array $relations);
has(string $relation);
whereHas(string $relation, closure $closure);
scopeQuery(Closure $scope);
getFieldsSearchable();
setPresenter($presenter);
skipPresenter($status = true);

```

*如果要使用数据库缓存，则数据库的操作必须通过repostory*

更多信息请查看: https://github.com/andersao/l5-repository


## BanyanDB使用规则

- Constants目录下定义操作的key 文件名称为 BanyanConstant.php
- Service目录下定义 BanyanService 负责BanyanDB业务

## Dingo

引用Dingo的模块主要是为了做路由的版本控制。 定义路由书写规则如下：
```
Route::version('v1', ['prefix' => 'prefix', 'middleware'=>'middlewareName', 'namespace' => 'Namespace'], function()
{
    Route::get('/', 'ResourcesController@ping');
}
```

- `prefix` 必须为 `api/projectName`, 便于以后路由的分发

- 建议大家定义API的时候，尽量使用restfull的方式定义

查看所有的路由信息:  `php artisan api:routes`


## 测试

框架已经mock了 thrift_client的rpc调用, 具体的例子请参考`PhotoRpcTest`

写完测试之后，执行`make test` 可以自动跑测试用例



## 打点统计

打点功能通过 `jiuyan-statsd`组件提供.

所有的api会自动打点统计api的相关信息， 需要先配置statsd

测试环境不会打点。


```
return array(
    'host' => '', // statsd的服务器地址
    'port' => ,
    'timeout' => 3,
    'namespace' => 'stats.apps.information',  // 打点前缀
);
```

## xhprof

xhprof 功能通过`jiuyan-lumen-profiler` 组件提供

```
return [
    'enable' => true,
    'dir' => '/var/log/xhprof',  // xhprof生成的目录地址
    'type' => '1'
];

```


## api用户信息获取

用户信息通过auth认证，如果用户处于登陆状态，会自动获取到用户信息
1. 支持mock用户信息, 前台配置文件 api_auth.php
```php
<?php

return [
    'thrift_client_uc_config_name' => 'user_center',  //框架中thrift client 配置的usercenter 配置服务名称 
    'is_mock' => true, //开启mock用户信息
];
```

2. 后台管理员登陆信息, 后台配置文件 sso_auth.php
```php
return [
    'callback' => '',
    'sso_base_domain' => '',
    'sso_domain' => '',
    'router_prefix' => ''
];
```

router_prefix 参数为Auth::user() 获得用户信息时 , 判断是管理员信息还是 用户信息

```
$user = Auth::user();
```

## qconf client
查看 APP_ENV, 如果等于 local 开启mock模式
查看 APP_QCONF_BASE_PATH, 为qconf根目录 默认in_men

```php
//env('APP_QCONF_BASE_PATH', '/in_men')
//env('APP_ENV') == 'local'  
         
$userAliases = [
    'Jiuyan\Laravel\QConf\Facades\QConfClient' => 'QConfClient'
];

$app->withFacades(true, $userAliases);
            
$app->register(\Jiuyan\Laravel\QConf\Providers\QConfClientServiceProvider::class);

use Jiuyan\Laravel\QConf\Facades\QConfClient;
       
QConfClient::getConf("/in_men/DB_PASSWORD");  //获得数据库的密码配置

```


## 请求签名验证
使用方式
```php
$app->routeMiddleware(
   [
       'jiuyan.sign' => \Jiuyan\Request\Tool\SignMiddleware::class
   ]
);

Route::version('v1', ['prefix' => '/api/comment', 'middleware'=>['jiuyan.sign'], 'namespace' => 'Modules\Comment\Http\Controllers'], function () {
    Route::get('/ping', 'ResourcesController@ping');

});

```
签名验证不通过,会抛SignException 异常!

## 请求参数验证 控制层执行
重新安装:lumen-modules 
返回验证后符合的结果 $this->validate($request, ['photo_id' => 'required']);
返回请求 return $this->response($data, $msg, $code);
返回错误 return $this->error($msg, $code);


## 代码规范检测与修复

检测: `make sniffer`
修复: `make fixer`

## 集成ID生成器模块
composer require jiuyan/laravel-id-generator 

```php
$app->register(Jiuyan\IdGenerator\Provider\IdGeneratorProvider::class);


/**
 * $name 锁的名称 也是 分表的表的名字 对应 keyName
 * $step 步长
 * $cachedStep 缓存管理的步长
 * $origin 出初id
 */
ApcIdGeneratorFactory::getInstance('这里是表名(来存储各个id端的数据结构的表)例如:common_generator')->getApcIdGenerator()->getNextId($name, $step = 1, $cachedStep = 100, $origin = 10000);

```
```mysql
 CREATE TABLE `common_generator` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyName` varchar(50) NOT NULL COMMENT '表名',
  `currentId` bigint(20) unsigned NOT NULL COMMENT '当前最大ID',
  `step` mediumint(9) NOT NULL COMMENT '步长',
  `cacheStep` mediumint(9) NOT NULL COMMENT '缓存步长',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`keyName`),
  KEY `currentid` (`currentId`)
) ENGINE=InnoDB AUTO_INCREMENT=966 DEFAULT CHARSET=utf8 COMMENT='ID生成器字段';

```

 


## 接口访问的全局参数

实现的功能如下

1. 打点统计
2. xhprof分析
3. ssoadmin 验证
4. api用户信息获取
5. 请求签名验证
6. 多模块支持, 模块分离
7. api文档生成及mock
8. 集成qconf client
9. 集成ID生成器模块
10. 代码规范检测与修复



## API文档生成

 1. npm install -g apidoc        
 2. npm install -g git+ssh://git@githost.in66.cc:xinghuo/apidoc-plugin-jiuyanschema.git    
 3. make doc (Makefile里设置了命令 默认是扫描 modules目录)
 4. wiki http://wiki.in66.cc/pages/viewpage.action?pageId=4410733
 5. php artisan json:scheme {res} , 生成 json scheme文件,  
 
 ## Rabbitmq
 
 php artisan module:make-job JobName ModuleName
 
 支持数组
 ```php
 class Message implements ShouldQueue
 {
     use InteractsWithQueue, SerializesModels, Queueable;
 
     /**
      * @var array
      */
     protected $data;
 
     /**
      * GuangDianTongJob constructor.
      *
      * $data key 值:
      *
      * - _idfa:
      * - type: ios, android
      * - _v: 应用版本
      * -_token:
      *
      * @param array $data
      */
     public function __construct(array $data)
     {
         $this->data = $data;
     }
 
     /**
      * @param Queue $queue
      */
     public function queue(Queue $queue)
     {
         $payload = array_merge($this->data, [
             'create_time' => time(),
             'from'        => 'message',
         ]);
 
         $queue->pushRaw(json_encode($payload), $this->queue);
     }
 }
 ```
 
 #异常处理
 bootstrap.php  修改 App\Exceptions\Handler::class
 ```php
 $app->singleton(
     Illuminate\Contracts\Debug\ExceptionHandler::class,
     App\Exceptions\Handler::class
 );
 ```

 #session开启
 在app 下service Provider中添加 如下 
 $this->app->configure('session');
 $this->app->instance(SessionManager::class, $this->app['session']);
 在bootstrap 中添加middleware 和 provider 
 
 ```php
 $app->middleware([
     \Illuminate\Session\Middleware\StartSession::class
 ]);
 
 $app->register(\Illuminate\Session\SessionServiceProvider::class);
 
 ```
 cache配置文件新增 store
 ```php
  'share-session' =>[
             'driver' => 'memcached',
             'servers' => [
                 [
                     'host' => env('MEMCACHED_SESSION_HOST_1', '127.0.0.1'),
                     'port' => env('MEMCACHED_SESSION_PORT_1', 11211),
                     'weight' => 100,
                 ],
                 [
                     'host' => env('MEMCACHED_SESSION_HOST_2', '127.0.0.1'),
                     'port' => env('MEMCACHED_SESSION_PORT_2', 11211),
                     'weight' => 100,
                 ],
                 [
                     'host' => env('MEMCACHED_SESSION_HOST_3', '127.0.0.1'),
                     'port' => env('MEMCACHED_SESSION_PORT_3', 11211),
                     'weight' => 100,
                 ],
             ],
             /**
              * 暂时不区分环境，直接写死了
              */
             'prefix' => 'd1_production-wx_v_1.0_session'
         ],
 ```
session配置文件设置 store = share-session

```php
<?php
/**
 * Created by IntelliJ IDEA.
 * User: topone4tvs
 * Date: 2017/3/8
 * Time: 16:59
 */
return [
    'driver' => env('SESSION_DRIVER', 'memcached'),//默认使用file驱动，你也可以在.env中配置
    'lifetime' => 2592000,//缓存失效时间
    'expire_on_close' => false,
    'store' => 'share-session',
    'encrypt' => false,
    'files' => storage_path('framework/session'),//file缓存保存路径
    'connection' => null,
    'table' => 'sessions',
    'lottery' => [2, 100],
    'cookie' => 'PHPSESSID',
    'path' => '/',
    'domain' => '',
    'secure' => false,
];
```

##cookie

设置 cookie函数 
```php
 $app->register(\Illuminate\Cookie\CookieServiceProvider::class);
 $app->singleton(\Illuminate\Contracts\Cookie\Factory::class, function($app){
     return $app['cookie'];
 });
    
   

// 读取cookie的方式  

    public function ping(Request $request)
    {
        echo $request->cookie('last-serviceName');

    }
    
//设置 
  
  use Dingo\Api\Http\Request;
  use Illuminate\Http\Response;
  use Nwidart\Modules\Routing\Controller as BaseController;
  
  class ResourcesController extends BaseController
  {
      public function ping(Request $request , Response $response)
      {
          $response
              ->cookie('name', 'value', 1);
          return $response->setContent("xxx");
      }
  }

```
