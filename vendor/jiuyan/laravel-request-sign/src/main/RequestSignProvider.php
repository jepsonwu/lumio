<?php
namespace Jiuyan\Request\Tool;

/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/8/9
 * Time: 下午4:08
 */
use Illuminate\Support\ServiceProvider;
use Jiuyan\Request\Tool\Impl\Sign;

class RequestSignProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SignInterface::class, function () {
            return (new SignFactory())->make(Sign::class);
        });
    }
}
