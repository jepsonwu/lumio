<?php

namespace Dingo\Api\Provider;

use Dingo\Api\Http\Parser\AcceptPrefix as AcceptPrefix;

class PathVersionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {


        $this->registerHttpParsers();

    }


    /**
     * Register the HTTP parsers.
     *
     * @return void
     */
    protected function registerHttpParsers()
    {
        $this->app->singleton(AcceptPrefix::class, function ($app) {
            return new AcceptPrefix(
                $this->config('standardsTree'),
                $this->config('subtype'),
                $this->config('version'),
                $this->config('defaultFormat'),
                $this->config('versionPathPattern')
            );
        });
    }

}
