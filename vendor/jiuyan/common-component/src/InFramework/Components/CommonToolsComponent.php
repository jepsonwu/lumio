<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/1
 * Time: 14:10
 */

namespace Jiuyan\Common\Component\InFramework\Components;

class CommonToolsComponent
{
    public static function getRouteUrl($routeName, $urlApiVersion = 'v1')
    {
        return app('Dingo\Api\Routing\UrlGenerator')->version($urlApiVersion)->route($routeName);
    }
}