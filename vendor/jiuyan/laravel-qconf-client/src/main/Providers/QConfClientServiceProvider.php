<?php

namespace Jiuyan\Laravel\QConf\Providers;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

use Jiuyan\Qconf\Client\JyQconf;
use Jiuyan\Qconf\Client\MockQconf;
use Jiuyan\Qconf\Client\QihooQconf;

/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/7/20
 * Time: 上午10:00
 */
class QConfClientServiceProvider extends IlluminateServiceProvider
{
    public function boot()
    {
        // TODO: Implement boot() method.
    }

    public function register()
    {
        $this->app->singleton('QConfClient', function () {
            $basePath = env('APP_QCONF_BASE_PATH', '/in_men');
            $qconf = new JyQconf();
            if (env('APP_ENV') == 'local') {
                $mockQconf = new MockQconf();
                $mockQconf->setMockEnv(app_path() . '/../.env', $basePath);
                $qconf->setQconf($mockQconf);
            } else {
                $qiHoo = new QihooQconf();
                $qconf->setQconf($qiHoo);
            }
            return $qconf;
        });
        // TODO: Implement register() method.
    }
}
