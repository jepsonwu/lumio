<?php

namespace Jiuyan\Laravel\Tool\Facades;

use Illuminate\Support\Facades\Facade;

class HashId extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tool.hashid';
    }
}
