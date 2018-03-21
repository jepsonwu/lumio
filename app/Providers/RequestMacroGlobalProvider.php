<?php

namespace App\Providers;

use App\Components\CustomizeCaptchaManageComponent;
use Illuminate\Contracts\Validation\Factory;
use Jiuyan\LumioSSO\Contracts\AuthenticateAdminContract;
use Jiuyan\LumioSSO\Contracts\AuthenticateContract;
use App\Exceptions\Handler;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;
use Exception;
use Jiuyan\Captcha\Providers\CaptchaServiceProvider;
use Jiuyan\Common\Component\InFramework\Providers\InFrameworkProvider;
use Jiuyan\Lumio\BanyanDB\Providers\BanyanDBServiceProvider;
use Modules\Account\Constants\AccountBanyanDBConstant;
use Modules\Account\Services\UserInternalService;
use Modules\Admin\Services\UserInternalService as AdminUserInternalService;

class RequestMacroGlobalProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerCommon();
        $this->registerSession();
        $this->registerSystem();
        $this->registerIdHelper();
        $this->registerBanyanDB();
        $this->registerAuth();
        $this->registerValidator();
        $this->registerAdmin();
    }

    protected function registerCommon()
    {
        $this->app->configure("common");
    }

    protected function registerSession()
    {
        $this->app->configure('session');
        $this->app->instance(SessionManager::class, $this->app['session']);
    }

    protected function registerSystem()
    {
        $this->app->configure('captcha');
        $this->app->register(CaptchaServiceProvider::class);
        $this->app->register(InFrameworkProvider::class);
        $this->app->singleton('CaptchaManageComponent', CustomizeCaptchaManageComponent::class);
        $this->app->singleton('banyandbForCaptcha', function () {
            return AccountBanyanDBConstant::common(null);
        });
    }

    protected function registerBanyanDB()
    {
        $this->app->configure('banyandb');
        $this->app->register(BanyanDBServiceProvider::class);
    }

    protected function registerAuth()
    {
        $this->app->configure('api_auth');
        $this->app->configure('admin_auth');
        /**
         * AUTH覆盖lib库中的取用户的实现，各个框架自己实现
         */
        $this->app->singleton(AuthenticateContract::class, UserInternalService::class);
        $this->app->singleton(AuthenticateAdminContract::class, AdminUserInternalService::class);
    }

    protected function registerIdHelper()
    {
        if ($this->app->environment() == "local") {
            $this->app->register(IdeHelperServiceProvider::class);
            AliasLoader::getInstance($this->app->make('config')->get('app.aliases', []))->register();
        }
    }

    //todo
    protected function registerValidator()
    {
        /**@var $validator Factory* */
//        $validator = app('validator');
//        $validator->extend('default', function ($attribute, $value, $parameters) {
//        });
    }

    protected function registerAdmin()
    {
        $this->app->configure("admin");
    }

    public function boot()
    {
        /** @var DingoExceptionHandle $handler */
        $handler = $this->app['api.exception'];
        $handler->register(function (Exception $exception) {
            $handler = app(Handler::class);
            return $handler->render(app('request'), $exception);
        });
    }
}
