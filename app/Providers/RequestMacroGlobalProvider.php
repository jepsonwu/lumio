<?php

namespace App\Providers;

use App\Components\CustomizeCaptchaManageComponent;
use Jiuyan\LumioSSO\Contracts\AuthenticateContract;
use App\Exceptions\Handler;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;
use Exception;
use Jiuyan\Captcha\Providers\CaptchaServiceProvider;
use Jiuyan\Common\Component\InFramework\Providers\InFrameworkProvider;
use Jiuyan\Lumio\BanyanDB\BanyanDBFactory;
use Jiuyan\Lumio\BanyanDB\Providers\BanyanDBServiceProvider;
use Modules\User\Contracts\UserExtensionsInternalServiceContract;
use Modules\User\Contracts\UserSatelliteInternalServiceContract;
use Modules\User\Contracts\UserTaskInternalServiceContract;
use Modules\User\Services\Internal\UserExtensionsInternalService;
use Modules\User\Services\Internal\UserTaskInternalService;
use Modules\User\Services\UserSatelliteInternalService;
use Modules\Account\Services\UserInternalService;

class RequestMacroGlobalProvider extends ServiceProvider
{
    public function register()
    {
        /**
         * TODO:: 暂时关闭session，避免发生冲突
         */
        $this->app->configure('session');
        $this->app->instance(SessionManager::class, $this->app['session']);

        $this->registerSession();
        $this->registerSystem();
        $this->registerIdHelper();
        $this->registerBanyanDB();
        $this->registerAuth();
        $this->registerInternalService();
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
            return BanyanDBFactory::getInstance('in_sms', 'auth_code', 'new', BanyanDBFactory::HASH_STRUCTURE);
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
        $this->app->configure('sso_auth');
        /**
         * AUTH覆盖lib库中的取用户的实现，各个框架自己实现
         */
        $this->app->singleton(AuthenticateContract::class, UserInternalService::class);
    }

    protected function registerIdHelper()
    {
        if ($this->app->environment() == "local") {
            $this->app->register(IdeHelperServiceProvider::class);
            AliasLoader::getInstance($this->app->make('config')->get('app.aliases', []))->register();
        }
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

    protected function registerInternalService()
    {
        $this->registerUserInternalService();
    }

    protected function registerUserInternalService()
    {
        $this->app->singleton(UserTaskInternalServiceContract::class, UserTaskInternalService::class);
        $this->app->singleton(UserExtensionsInternalServiceContract::class, UserExtensionsInternalService::class);
        $this->app->singleton(UserSatelliteInternalServiceContract::class, UserSatelliteInternalService::class);
    }
}
