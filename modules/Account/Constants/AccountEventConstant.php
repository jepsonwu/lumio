<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/18
 * Time: 14:53
 */

namespace Modules\Account\Constants;

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

class AccountEventConstant
{
    const REGISTER_SUCCESS = RegisterSuccessEvent::class;
    const REGISTER_FAILED = RegisterFailedEvent::class;
    const REGISTER_REPEAT = RegisterRepeatEvent::class;
    const REGISTER_FORCE_FAILED = RegisterForceFailedEvent::class;

    const THIRD_PARTY_REGISTER_SUCCESS = ThirdPartyRegisterSuccessEvent::class;
    const THIRD_PARTY_LOGIN_SUCCESS = ThirdPartyLoginSuccessEvent::class;
    const THIRD_PARTY_BIND_SUCCESS = ThirdPartyBindSuccessEvent::class;
    const THIRD_PARTY_UNBIND_SUCCESS = ThirdPartyUnBindSuccessEvent::class;

    const ACCOUNT_CAPTCHA_CHECK_START = AccountCaptchaCheckStartEvent::class;
    const ACCOUNT_CAPTCHA_CHECK_FAILED = AccountCaptchaCheckFailedEvent::class;
    const ACCOUNT_CAPTCHA_CHECK_SUCCESS = AccountCaptchaCheckSuccessEvent::class;
    const ACCOUNT_CAPTCHA_DEAL_SUCCESS = AccountCaptchaDealSuccessEvent::class;
    const ACCOUNT_CAPTCHA_DEAL_FAILED = AccountCaptchaDealFailedEvent::class;
    const ACCOUNT_MOBILE_BIND_SUCCESS = AccountMobileBindSuccessEvent::class;
    const ACCOUNT_MOBILE_CHANGE_SUCCESS = AccountMobileChangeSuccessEvent::class;
}