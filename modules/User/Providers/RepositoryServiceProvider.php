<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(\Modules\User\Repositories\UserTaskRepository::class, \Modules\User\Repositories\UserTaskRepositoryEloquent::class);
        //:end-bindings:
    }
}
