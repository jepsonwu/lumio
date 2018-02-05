<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/17
 * Time: 10:38
 */

namespace Modules\Account\Providers;

use Jiuyan\Common\Component\InFramework\Providers\JThriftBaseServiceProvider;

class ThriftServiceProvider extends JThriftBaseServiceProvider
{
    public function register()
    {
        $this->app->singleton('UserInfoThriftService', function () {
            return $this->getThriftDao('userCenter');
        });
        $this->app->singleton('OpenPlatformService', function () {
            return $this->getThriftDao('openCenter');
        });
        $this->app->singleton('InUserService', function () {
            return $this->getThriftDao('inServerCenter')->setRetry(2)->setServiceName('NUserService');
        });
    }
}