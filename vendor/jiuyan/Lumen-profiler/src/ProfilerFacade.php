<?php
namespace Jiuyan\Profiler;

use Illuminate\Support\Facades\Facade;

class ProfilerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'profiler';
    }
}
