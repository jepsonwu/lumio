<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/13
 * Time: 16:52
 */

namespace Modules\Account\Constants;

class AccountBusinessConstant
{
    const ACCOUNT_AUTHORIZED_URL_KEY = '_token';
    const ACCOUNT_AUTHORIZED_COOKIE_COMMON_KEY = 'tg_auth';
    const ACCOUNT_AUTHORIZED_COOKIE_SPE_KEY = '_token';
    const ACCOUNT_LOGIN_STATUS_URL_KEY = '_aries';

    const COMMON_ACCOUNT_TYPE_MOBILE = 8;
    const COMMON_ACCOUNT_TYPE_IN_NUMBER = 9;

    const COMMON_ACCOUNT_TYPE_FOR_UC_MOBILE = 1;
    const COMMON_ACCOUNT_TYPE_FOR_UC_IN_NUMBER = 2;

    const COMMON_ACCOUNT_AUTH_TYPE_MOBILE_REGISTER = 'mobile_register';
    const COMMON_ACCOUNT_AUTH_TYPE_NORMAL_LOGIN = 'normal_login';

    const COMMON_REGULAR_PASSWORD_FORMAT = '/^(?![a-zA-z]+$)(?!\d+$)[a-zA-z0-9!@#$^&*_+.]{6,16}$/';
    const COMMON_REGULAR_IN_NUMBER_FORMAT = '/^[a-z\d_]{3,20}$/i';

    const COMMON_ACCOUNT_PASSWORD_RESET_TIMES_LIMIT_PER_DAY = 5;

    /**
     * 对接in原有代码逻辑中对于sysqueue中消息类型的定义规则
     */
    const COMMON_SYS_QUEUE_MSG_TYPE_SMS_LOG = '17';

    const COMMON_REGISTER_SMS_SEND_STAT_CHECK_START = 'checkcode';
    const COMMON_REGISTER_SMS_SEND_STAT_CHECK_SUCCESS = 'ok';
    const COMMON_REGISTER_SMS_SEND_STAT_CHECK_FAILED = 'fail';
    const COMMON_REGISTER_SMS_SEND_STAT_REGISTER_SUCCESS = 'success';
    const COMMON_REGISTER_SMS_SEND_STAT_REGISTER_NEW = 'newuser';
    const COMMON_REGISTER_SMS_SEND_STAT_REGISTER_ERROR = 'error';
}