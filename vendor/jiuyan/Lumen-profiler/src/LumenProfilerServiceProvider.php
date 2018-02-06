<?php

namespace Jiuyan\Profiler;

use Illuminate\Support\ServiceProvider;

class LumenProfilerServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Register Service.
     */
    public function register()
    {
        $this->registerNamespaces();
        $this->registerServices();
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $this->app->configure('profiler');

        $configPath = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($configPath, 'profiler');

        $this->publishes([
            $configPath => config_path('profiler.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    protected function registerServices()
    {
        $this->app->singleton('profiler', function ($app) {
            $config = $app['config']->get('profiler');

            return new Profiler($config);
        });
    }
}
