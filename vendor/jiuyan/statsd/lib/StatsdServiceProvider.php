<?php

namespace Domnikl\Statsd;

use Illuminate\Support\ServiceProvider;

class StatsdServiceProvider extends ServiceProvider
{
    public function configure()
    {
     $this->app->configure('statsd');

     $configPath = __DIR__ . '/../config/config.php';

     $this->mergeConfigFrom($configPath, 'statsd');

     $this->publishes([
         $configPath => config_path('statsd.php'),
        ], 'config');
    }

    public function register()
    {
        $this->configure();
        $this->app->singleton('statsd', function () {

        $options = array(
            'host' => 'localhost',
            'port' => 8125,
            'timeout' => 3,
            'namespace' => 'stats.apps.app',
        );

        $config = $this->app['config']['statsd'];

        if (isset($config['host'])) {
            $options['host'] = $config['host'];
        }
        if (isset($config['port'])) {
            $options['port'] = $config['port'];
        }
        if (isset($config['namespace'])) {
            $options['namespace'] = $config['namespace'];
        }
        if (isset($config['timeout'])) {
            $options['timeout'] = $config['timeout'];
        }

        $connection = new Connection\UdpSocket($options['host'], $options['port'], $options['timeout']);

        return new Client($connection, $options['namespace']);
       });
    }
}
