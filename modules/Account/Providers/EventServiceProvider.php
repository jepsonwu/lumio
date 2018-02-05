<?php

namespace Modules\Account\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Modules\Account\Events\Register\RegisterFailedEvent;
use Modules\Account\Listeners\Register\RegisterFailedListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RegisterFailedEvent::class => [
            RegisterFailedListener::class
        ]
    ];
}
