<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/12
 * Time: 18:02
 */

namespace Jiuyan\Captcha\Providers;

use Illuminate\Support\ServiceProvider;
use Jiuyan\Captcha\CaptchaManageComponent;
use Jiuyan\CommonCache\BanyanFactory;

class CaptchaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/captcha.php'), 'api');
        $this->app->singleton('banyandbForCaptcha', function () {
            return BanyanFactory::getInstance('in_sms', 'auth_code', 'in_peanut_captcha', BanyanFactory::KEY_STRUCTURE);
        });
        $this->app->singleton('CaptchaManageComponent', CaptchaManageComponent::class);
    }
}
