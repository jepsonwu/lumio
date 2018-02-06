<?php

namespace Jiuyan\Laravel\Tool\Providers;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class HashIdServiceProvider extends IlluminateServiceProvider
{
    protected function registerConfig()
    {
        $this->app->configure('hashids');
        $path = realpath(__DIR__ . '/../../../config/hashdis.php');
        $this->mergeConfigFrom($path, 'hashids');
        $this->publishes([
            $path => config_path('hashids.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->app->singleton('tool.hashid', function ($app) {
            $salt = config('hashids.salt', '');
            $minLen = config('hashids.minLen', 16);
            $alphabet = config('hashids.alphabet', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

            $hashids = new \Hashids\Hashids($salt, $minLen, $alphabet);
            return $hashids;
        });

    }
}
