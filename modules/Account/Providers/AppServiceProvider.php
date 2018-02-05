<?php

namespace Modules\Account\Providers;

use Illuminate\Support\ServiceProvider;
use Jiuyan\Socialite\In\Provider as InProvider;
use SocialiteProviders\Weixin\Provider as WeiXinProvider;
use SocialiteProviders\Weibo\Provider as WeiboProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    public function boot()
    {
    }
}
