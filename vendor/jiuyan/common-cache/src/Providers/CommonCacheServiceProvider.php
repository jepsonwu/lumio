<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/12
 * Time: 18:02
 */

namespace Jiuyan\CommonCache\Providers;

use Illuminate\Support\ServiceProvider;

class CommonCacheServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/common_cache.php'), 'common_cache');
    }
}
