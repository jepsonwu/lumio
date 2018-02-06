<?php

namespace Jiuyan\LumioSSO\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Jiuyan\LumioSSO\Services\AdminAuthService;
use Jiuyan\LumioSSO\Contracts\AuthenticateAdminContract;
use Jiuyan\LumioSSO\Contracts\AuthenticateContract;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../../config/api_auth.php'), 'api_auth');
        $this->mergeConfigFrom(realpath(__DIR__ . '/../../config/sso_auth.php'), 'sso_auth');

        $this->app->singleton(AuthenticateAdminContract::class, function ($app) {
            return new AdminAuthService();
        });
    }


    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function (Request $request) {
            /**
             * @var AuthenticateContract $authService
             */
            if (strpos($request->path(), config('sso_auth.router_prefix')) === 0) {
                $authService = app(AuthenticateAdminContract::class);
            } else {
                $authService = app(AuthenticateContract::class);
            }

            if (config('api_auth.is_mock')) {
                $authService->setMock(true);
            }

            return $authService->getLoginUser();
        });
    }
}
