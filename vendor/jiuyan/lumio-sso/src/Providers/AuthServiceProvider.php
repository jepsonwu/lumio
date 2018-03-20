<?php

namespace Jiuyan\LumioSSO\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
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
        $this->mergeConfigFrom(realpath(__DIR__ . '/../../config/admin_auth.php'), 'admin_auth');
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
            if (strpos("/" . $request->path(), config('admin_auth.router_prefix')) === 0) {
                $authService = app(AuthenticateAdminContract::class);
                $auth = 'admin_auth';
            } else {
                $authService = app(AuthenticateContract::class);
                $auth = 'api_auth';
            }

            if (config($auth . '.is_mock')) {
                $authService->setMock(true);
            }

            return $authService->getLoginUser();
        });
    }
}
