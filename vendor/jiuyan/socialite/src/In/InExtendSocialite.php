<?php
namespace Jiuyan\Socialite\In;

use SocialiteProviders\Manager\SocialiteWasCalled;

class InExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'in', __NAMESPACE__.'\Provider'
        );
    }
}
