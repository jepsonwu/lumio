<?php
/**
 * Created by PhpStorm.
 * User: jianpingwu
 * Date: 2017/11/29
 * Time: 上午11:56
 */
return [
    'locale' => env('APP_LOCALE', 'zh_CN'),
    'log_level' => env('APP_LOG_LEVEL', 'error'),
    'aliases' => [
        'Illuminate\Support\Facades\App' => 'App',
        'Illuminate\Support\Facades\Config' => 'Config',
        'Illuminate\Support\Facades\File' => 'File',
        'Illuminate\Support\Facades\Request' => 'Request',
        'Illuminate\Support\Facades\Cache' => 'Cache',
        'Jiuyan\Profiler\ProfilerFacade' => 'Profiler',
        'Dingo\Api\Facade\Route' => 'Route',
        'Domnikl\Statsd\StatsdFacade' => 'Statsd',
        'Jiuyan\Laravel\Tool\Facades\HashId' => 'HashId',
        'Jiuyan\Laravel\Nosql\Facade\Ssdb' => 'SSDB',
        'Jiuyan\Laravel\QConf\Facades\QConfClient' => 'QConfClient',
        'Illuminate\Support\Facades\Log' => 'Log',
        'Jiuyan\Common\Component\InFramework\Facades\ParamsToolFacade' => 'ParamsTool',
        'Illuminate\Support\Facades\Auth' => 'Auth',
    ]
];
