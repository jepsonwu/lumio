<?php
/**
 * Created by IntelliJ IDEA.
 * User: topone4tvs
 * Date: 2017/3/7
 * Time: 19:14
 */

namespace Jiuyan\Common\Component\InFramework\Facades;

use Illuminate\Support\Facades\Facade;

class ParamsToolFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ParamsTool';
    }
}