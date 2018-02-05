<?php

namespace Modules\Account\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Modules\Account\Events\Account\AccountCaptchaCheckFailedEvent;
use Modules\Account\Events\Account\AccountCaptchaCheckStartEvent;
use Modules\Account\Events\Account\AccountCaptchaCheckSuccessEvent;
use Modules\Account\Events\Account\AccountCaptchaDealFailedEvent;
use Modules\Account\Events\Account\AccountCaptchaDealSuccessEvent;
use Modules\Account\Events\Account\AccountMobileBindSuccessEvent;
use Modules\Account\Events\Account\AccountMobileChangeSuccessEvent;
use Modules\Account\Events\Register\RegisterFailedEvent;
use Modules\Account\Events\Register\RegisterForceFailedEvent;
use Modules\Account\Events\Register\RegisterRepeatEvent;
use Modules\Account\Events\Register\RegisterSuccessEvent;
use Modules\Account\Events\Register\ThirdPartyBindSuccessEvent;
use Modules\Account\Events\Register\ThirdPartyLoginSuccessEvent;
use Modules\Account\Events\Register\ThirdPartyRegisterSuccessEvent;
use Modules\Account\Events\Register\ThirdPartyUnBindSuccessEvent;
use Modules\Account\Listeners\Account\AccountAuthFinishListener;
use Modules\Account\Listeners\Account\AccountCaptchaCheckFailedListener;
use Modules\Account\Listeners\Account\AccountCaptchaCheckStartListener;
use Modules\Account\Listeners\Account\AccountCaptchaCheckSuccessListener;
use Modules\Account\Listeners\Account\AccountCaptchaDealFailedListener;
use Modules\Account\Listeners\Account\AccountCaptchaDealSuccessListener;
use Modules\Account\Listeners\Account\AccountMobileBindSuccessListener;
use Modules\Account\Listeners\Account\AccountMobileChangeSuccessListener;
use Modules\Account\Listeners\Common\ThirdPartyBindTaskFinishListener;
use Modules\Account\Listeners\Register\RegisterFailedListener;
use Modules\Account\Listeners\Register\RegisterRepeatListener;
use Modules\Account\Listeners\Register\RegisterSuccessListener;
use Modules\Account\Listeners\Register\ThirdPartyBindSuccessListener;
use Modules\Account\Listeners\Register\ThirdPartyLoginSuccessListener;
use Modules\Account\Listeners\Register\ThirdPartyRegisterSuccessListener;
use Modules\Account\Listeners\Register\ThirdPartyUnBindSuccessListener;

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
        ],
        RegisterForceFailedEvent::class => [],
        RegisterRepeatEvent::class => [
            RegisterRepeatListener::class
        ],
        RegisterSuccessEvent::class => [
            RegisterSuccessListener::class,
            AccountAuthFinishListener::class,
        ],
        ThirdPartyLoginSuccessEvent::class => [
            ThirdPartyLoginSuccessListener::class,
            ThirdPartyBindTaskFinishListener::class
        ],
        ThirdPartyRegisterSuccessEvent::class => [
            ThirdPartyRegisterSuccessListener::class,
            ThirdPartyBindTaskFinishListener::class
        ],
        ThirdPartyBindSuccessEvent::class => [
            ThirdPartyBindSuccessListener::class,
            ThirdPartyBindTaskFinishListener::class
        ],
        ThirdPartyUnBindSuccessEvent::class => [
            ThirdPartyUnBindSuccessListener::class
        ],
        AccountCaptchaCheckStartEvent::class => [
            AccountCaptchaCheckStartListener::class
        ],
        AccountCaptchaCheckFailedEvent::class => [
            AccountCaptchaCheckFailedListener::class
        ],
        AccountCaptchaCheckSuccessEvent::class => [
            AccountCaptchaCheckSuccessListener::class
        ],
        AccountCaptchaDealSuccessEvent::class => [
            AccountCaptchaDealSuccessListener::class
        ],
        AccountCaptchaDealFailedEvent::class => [
            AccountCaptchaDealFailedListener::class
        ],
        AccountMobileBindSuccessEvent::class => [
            AccountMobileBindSuccessListener::class,
            AccountAuthFinishListener::class,
        ],
        AccountMobileChangeSuccessEvent::class => [
            AccountMobileChangeSuccessListener::class,
            AccountAuthFinishListener::class,
        ]
    ];
}
