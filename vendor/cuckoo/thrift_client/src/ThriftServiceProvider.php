<?php
namespace Jiuyan\Cuckoo\ThriftClient;

use Illuminate\Support\ServiceProvider;

class ThriftServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Manager::setLogger($this->app['log']);
        Manager::setCacher($this->app['cache']);
    }

    public function register()
    {
        $this->configure();

        $this->app->singleton('thrift.factory', function($app) {
            return new ClientFactory($app['config']['rpc']['default'], $app);
        });

        $this->app->singleton('thrift.manager', function($app) {
            return new Manager($app['thrift.factory'], $app['config']['rpc']);
        });

        $this->app->singleton('thrift.dao', function($app) {
            $dao = new ThriftDao();
            $dao->setManager($app['thrift.manager']);
            return $dao;
        });
    }

    public function configure()
    {
        // laravel 和 lumen不一样
        if (method_exists($this->app, 'configure')) {
            $this->app->configure('rpc');
        }

        $configPath = __DIR__ . '/../config/config.php';
        $this->mergeConfigFrom($configPath, 'rpc');
        $this->publishes([
         $configPath => config_path('rpc.php'),
        ], 'config');
        }
}
