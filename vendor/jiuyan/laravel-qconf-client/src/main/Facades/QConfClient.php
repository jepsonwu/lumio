<?php

namespace Jiuyan\Laravel\QConf\Facades;

use Illuminate\Support\Facades\Facade;

class QConfClient extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'QConfClient';
    }
}
