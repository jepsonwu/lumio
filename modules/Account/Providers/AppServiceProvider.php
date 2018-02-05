<?php

namespace Modules\Account\Providers;

use Illuminate\Support\ServiceProvider;
use Jiuyan\Socialite\In\Provider as InProvider;
use SocialiteProviders\Weixin\Provider as WeiXinProvider;
use SocialiteProviders\Weibo\Provider as WeiboProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (class_exists(RepositoryServiceProvider::class)) {
            $this->app->register(RepositoryServiceProvider::class);
        }
    }

    public function boot()
    {
        $this->app->make('Laravel\Socialite\Contracts\Factory')->extend('weibo', function ($app) {
            $config = $app['config']['account.service.weibo'];
            $provider = new WeiboProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect']
            );
            return $provider;
        });
        $this->app->make('Laravel\Socialite\Contracts\Factory')->extend('qq', function ($app) {
            $config = $app['config']['account.service.qq'];
            $provider = new WeiXinProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect']
            );
            $provider->setOpenId($app['request']->input('open_id'));
            return $provider;
        });
        $this->app->make('Laravel\Socialite\Contracts\Factory')->extend('weixin', function ($app) {
            $config = $app['config']['account.service.weixin'];
            $provider = new WeiXinProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect']
            );
            $provider->setOpenId($app['request']->input('open_id'));
            return $provider;
        });
        $this->app->make('Laravel\Socialite\Contracts\Factory')->extend('in', function ($app) {
            $config = $app['config']['account.service.in'];
            $provider = new InProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect']
            );
            return $provider;
        });
    }
}
