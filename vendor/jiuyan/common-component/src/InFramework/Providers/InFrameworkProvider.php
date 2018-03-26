<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/10
 * Time: 14:18
 */

namespace Jiuyan\Common\Component\InFramework\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Jiuyan\Common\Component\InFramework\Components\RequestParamsComponent;
use Jiuyan\Common\Component\InFramework\Services\RequestCommonParamsService;
use Jiuyan\Tools\Business\ParamsTool as ParamsToolComponent;
use ParamsTool;

class InFrameworkProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('ParamsTool', ParamsToolComponent::class);
        $this->app->singleton('RequestCommonParams', RequestParamsComponent::class);
        $this->registerThrift();
        $this->registerCommon();
        $this->registerCors();
    }

    public function registerCommon()
    {
        $this->app->singleton('RequestCommonParamsService', function () {
            return RequestCommonParamsService::getInstance();
        });
    }

    public function registerThrift()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../../../config/thrift.php'), 'thrift');
    }

    public function registerCors()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../../../config/cors.php'), 'cors');
    }

    public function boot()
    {
        Validator::extend('mobile', function ($attribute, $value, $parameters, $validator) {
            if (ParamsTool::mobile($value)) {
                return true;
            }
            return false;
        });
    }
}