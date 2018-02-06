<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/11/29
 * Time: 下午5:50
 */

namespace Jiuyan\Lumio\BanyanDB\Providers;

use Illuminate\Support\ServiceProvider;
use Jepsonwu\banyanDB\BanyanFactory;

class BanyanDBServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../../config/banyandb.php'), 'banyandb');

        BanyanFactory::registerGenerateBanyanCallback(config('banyandb.handle'));
    }
}
