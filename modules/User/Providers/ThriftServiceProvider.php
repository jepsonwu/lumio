<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/17
 * Time: 10:38
 */

namespace Modules\User\Providers;

use Jiuyan\Common\Component\InFramework\Providers\JThriftBaseServiceProvider;

class ThriftServiceProvider extends JThriftBaseServiceProvider
{
    public function register()
    {
        $this->app->singleton('InUserTaskService', function () {
            return $this->getThriftDao('inServerCenter')->setRetry(2)->setServiceName('NUserTaskService');
        });
        $this->app->singleton('InUserPasterService', function () {
            return $this->getThriftDao('inServerCenter')->setRetry(2)->setServiceName('NUserPasterLogService');
        });
    }
}