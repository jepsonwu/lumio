<?php

namespace Modules\Common\Services;

use Jiuyan\Captcha\CaptchaComponent;
use Jiuyan\Common\Component\InFramework\Components\ExceptionResponseComponent;
use Modules\Account\Constants\AccountErrorConstant;
use Jiuyan\Common\Component\InFramework\Services\BaseService;

class CommonService extends BaseService
{
    public function sendAccountCaptcha($mobile)
    {
        $result = CaptchaComponent::getInstance()->sendCaptcha($mobile, '', 'sms');
        if (!$result) {
            ExceptionResponseComponent::business(AccountErrorConstant::ERR_ACCOUNT_SMS_CODE_SEND_FAILED);
        }
    }
}