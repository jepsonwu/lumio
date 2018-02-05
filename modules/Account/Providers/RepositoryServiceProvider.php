<?php

namespace Modules\Account\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Account\Repositories\UserRepository;
use Modules\Account\Repositories\UserRepositoryEloquent;
use Modules\Account\Services\UserService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\Modules\Account\Repositories\AccountRepository::class, \Modules\Account\Repositories\AccountRepositoryEloquent::class);
        $this->app->singleton(UserRepository::class, UserRepositoryEloquent::class);
    }
}
