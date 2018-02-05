<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
}

$app = new Laravel\Lumen\Application(realpath(__DIR__ . '/../'));

$basePath = app()->basePath();

$app->configureMonologUsing(function ($logger) use ($app) {
    $app->configure('log');
    $writer = new App\Logger\Writer();
    $writer->setMonolog($logger);
    $writer->pushProcessor();
    if (php_sapi_name() == 'cli') {
        $writer->useDailyFiles(Config::get('log.console_path'), 30, env('APP_LOG_LEVEL', 'error'));
    } else {
        $writer->useDailyFiles(Config::get('log.path'), 30, env('APP_LOG_LEVEL', 'error'));
    }
    return $logger;
});

$app->instance('path.config', $basePath . DIRECTORY_SEPARATOR . 'config');
$app->instance('path.storage', $basePath . DIRECTORY_SEPARATOR . 'storage');
$app->instance('path.public', $basePath . DIRECTORY_SEPARATOR . 'public');
$app->instance('path.resources', $basePath . DIRECTORY_SEPARATOR . 'resources');
$app->instance('path.lang', $basePath . DIRECTORY_SEPARATOR . 'lang');

$userAliases = [
    'Illuminate\Support\Facades\App' => 'App',
    'Illuminate\Support\Facades\Config' => 'Config',
    'Illuminate\Support\Facades\File' => 'File',
    'Illuminate\Support\Facades\Request' => 'Request',
    'Illuminate\Support\Facades\Cache' => 'Cache',
    'Jiuyan\Profiler\ProfilerFacade' => 'Profiler',
    'Dingo\Api\Facade\Route' => 'Route',
    'Domnikl\Statsd\StatsdFacade' => 'Statsd',
    'Jiuyan\Laravel\Tool\Facades\HashId' => 'HashId',
    'Jiuyan\Laravel\QConf\Facades\QConfClient' => 'QConfClient',

    'Jiuyan\Common\Component\InFramework\Facades\ParamsToolFacade' => 'ParamsTool',
];

$app->withFacades(true, $userAliases);

$app->withEloquent();

$app->middleware([
    \Illuminate\Session\Middleware\StartSession::class
]);
$app->routeMiddleware(
    [
        'jiuyan.api.auth' => \Jiuyan\LumioSSO\Middlewares\ApiAuthMiddleware::class,
        'jiuyan.api.sign' => \Jiuyan\Common\Component\InFramework\Middleware\RequestSignatureMiddleware::class
    ]
);


$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);


$app->alias('translator', Illuminate\Contracts\Translation\Translator::class);

/**
 * 添加其它服务容器的时候，注意加载顺序
 */
$app->register(Dingo\Api\Provider\LumenServiceProvider::class);
$app->register(Jiuyan\Request\Tool\RequestSignProvider::class);
$app->register(\Jiuyan\LumioSSO\Providers\AuthServiceProvider::class);
$app->register(\Jiuyan\Laravel\Tool\Providers\HashIdServiceProvider::class);
$app->register(Jiuyan\IdGenerator\Provider\IdGeneratorProvider::class);
$app->register(Nord\Lumen\Cors\CorsServiceProvider::class);
$app->register(\Illuminate\Session\SessionServiceProvider::class);
$app->register(Jiuyan\Profiler\LumenProfilerServiceProvider::class);
$app->register(Laravel\Socialite\SocialiteServiceProvider::class);
$app->register(Nwidart\Modules\LumenModulesServiceProvider::class);
$app->register(Prettus\Repository\Providers\LumenRepositoryServiceProvider::class);
$app->register(Domnikl\Statsd\StatsdServiceProvider::class);
$app->register(App\Providers\RequestMacroGlobalProvider::class);
$app->register(VladimirYuldashev\LaravelQueueRabbitMQ\LaravelQueueRabbitMQServiceProvider::class);
$app->register(Jiuyan\Cuckoo\ThriftClient\ThriftServiceProvider::class);
$app->register(Illuminate\Cache\CacheServiceProvider::class);
//$app->register(\Jiuyan\Laravel\QConf\Providers\QConfClientServiceProvider::class);
$app->register(\Illuminate\Cookie\CookieServiceProvider::class);

$app->singleton(\Illuminate\Contracts\Cookie\Factory::class, function ($app) {
    return $app['cookie'];
});

//$app->register(ZlLaravelQueueKafka\LaravelQueueKafkaServiceProvider::class);
//$app->register(VladimirYuldashev\LaravelQueueRabbitMQ\LaravelQueueRabbitMQServiceProvider::class);

return $app;
