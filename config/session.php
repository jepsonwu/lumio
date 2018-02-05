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
    'cookie' => 'LUMIOPHPSESSID',
    'path' => '/',
    'domain' => '',
    'secure' => false,
];