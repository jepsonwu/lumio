<?php

namespace Modules\Common\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (class_exists(RepositoryServiceProvider::class)) {
            $this->app->register(RepositoryServiceProvider::class);
        }
    }
}
