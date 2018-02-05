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

    const COMMON_USER_TASK_STATUS_INIT = '0';
    const COMMON_USER_TASK_STATUS_ONE = '1';
    const COMMON_USER_TASK_STATUS_TWO = '2';
    const COMMON_USER_TASK_STATUS_THREE = '3';

    const COMMON_THIRD_PARTY_SOURCE_WEIBO = 1;
    const COMMON_THIRD_PARTY_SOURCE_QQ = 2;
    const COMMON_THIRD_PARTY_SOURCE_WEIXIN = 3;

    const COMMON_THIRD_PARTY_FLAG_WEIBO = 'weibo';
    const COMMON_THIRD_PARTY_FLAG_QQ = 'qq';
    const COMMON_THIRD_PARTY_FLAG_WEIXIN = 'weixin';

    const COMMON_USER_TASK_STATUS_AUTH_MOBILE = 1;
    const COMMON_USER_TASK_STATUS_UPLOAD_CONTACT = 1;

    const COMMON_ACCOUNT_TYPE_MOBILE = 8;
    const COMMON_ACCOUNT_TYPE_IN_NUMBER = 9;

    const COMMON_ACCOUNT_TYPE_FOR_UC_MOBILE = 1;
    const COMMON_ACCOUNT_TYPE_FOR_UC_IN_NUMBER = 2;

    const COMMON_ACCOUNT_AUTH_TYPE_MOBILE_REGISTER = 'mobile_register';
    const COMMON_ACCOUNT_AUTH_TYPE_NORMAL_LOGIN = 'normal_login';

    const COMMON_ACCOUNT_AUTH_TYPE_PARTNER_THIRD_PARTY_REGISTER = 'partner_third_party_register';
    const COMMON_ACCOUNT_AUTH_TYPE_PARTNER_COMMON_LOGIN = 'partner_common_login';
    const COMMON_ACCOUNT_AUTH_TYPE_NORMAL_AUTH = 'normal_auth';
    const COMMON_ACCOUNT_AUTH_TYPE_NORMAL_THIRD_PARTY_REGISTER = 'normal_third_party_register';
    const COMMON_ACCOUNT_AUTH_TYPE_NORMAL_THIRD_PARTY_LOGIN = 'normal_third_party_login';
    const COMMON_ACCOUNT_AUTH_TYPE_NORMAL_THIRD_PARTY_BIND = 'normal_third_party_bind';
    const COMMON_ACCOUNT_AUTH_TYPE_NORMAL_THIRD_PARTY_UNBIND = 'normal_third_party_unbind';
    const COMMON_ACCOUNT_AUTH_TYPE_NORMAL_SEARCH = 'normal_search';
    const COMMON_ACCOUNT_AUTH_TYPE_MOBILE_CHANGE = 'mobile_change';

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

    //UC异常
    const COMMON_UC_EXCEPTION_EXCEPTION = 'UC_EXCEPTION';
    //用户不存在
    const COMMON_UC_EXCEPTION_USER_NOT_EXIST = 'UC_0001';
    //JSON格式错误
    const COMMON_UC_EXCEPTION_JSON_FORMAT_ERROR = 'UC_0002';
    //第三方类型错误
    const COMMON_UC_EXCEPTION_TYPE_ERROR = 'UC_0003';
    //已绑定
    const COMMON_UC_EXCEPTION_ALREADY_BINDING = 'UC_0004';
    //第三方账户已被占用
    const COMMON_UC_EXCEPTION_SOURCE_ID_ALREADY_USED = 'UC_0005';
    //无绑定
    const COMMON_UC_EXCEPTION_NOT_BINDING = 'UC_0006';
    //解绑最后一个第三方账户时要设置密码
    const COMMON_UC_EXCEPTION_YOU_SHOULD_SET_PASSWORD = 'UC_0007';
    //手机号已被其它用户使用
    const COMMON_UC_EXCEPTION_MOBILE_USED_BY_ANOTHER_USER = 'UC_0008';
    //修改密码时，旧密码错误
    const COMMON_UC_EXCEPTION_OLD_PASSWORD_ERROR = 'UC_0012';
    //IN号已被占用
    const COMMON_UC_EXCEPTION_IN_NUMBER_USED = 'UC_0016';
    //手机格式错误
    const COMMON_UC_EXCEPTION_MOBILE_FORMAT_ERROR = 'UC_0017';
    //IN号格式错误
    const COMMON_UC_EXCEPTION_IN_NUMBER_FORMAT_ERROR = 'UC_0018';
    //登陆、注册、设置密码时，密码格式错误
    const COMMON_UC_EXCEPTION_PASSWORD_FORMAT_ERROR = 'UC_0019';
    //user表中task_status字段有误
    const COMMON_UC_EXCEPTION_TASK_STATUS_ERROR = 'UC_0020';
    //修改IN号时，提醒IN号已编辑过
    const COMMON_UC_EXCEPTION_IN_NUMBER_EDITED = 'UC_0021';
    //调用通用update时，参数不匹配，不支持更新
    const COMMON_UC_EXCEPTION_USER_UPDATE_NOT_SUPPORTED = 'UC_0022';
    //更换手机号时，提供的老用户id不正确
    const COMMON_UC_EXCEPTION_OLD_USER_NOT_EXIST = 'UC_0023';
    //更换手机号时，提供的老用户id与手机号不匹配
    const COMMON_UC_EXCEPTION_OLD_ID_AND_MOBILE_NOT_MATCH = 'UC_0024';
    // 密码没设置
    const COMMON_UC_EXCEPTION_PASSWORD_NOT_SET = 'UC_0026';
    //用户名和密码不匹配
    const COMMON_UC_EXCEPTION_USER_AND_PASSWORD_NOT_MATCH = 'UC_0027';
    //修改密码时，旧密码格式错误
    const COMMON_UC_EXCEPTION_OLD_PASSWORD_FORMAT_ERROR = 'UC_0028';
    //修改密码时，新密码格式错误
    const COMMON_UC_EXCEPTION_NEW_PASSWORD_FORMAT_ERROR = 'UC_0029';
    //设置密码时，发现密码已设
    const COMMON_UC_EXCEPTION_PASSWORD_ALREADY_SET = 'UC_0030';
    //更改task_status时，operator不正确
    const COMMON_UC_EXCEPTION_INVALID_OPERATOR = 'UC_0031';
    //IN号不能是手机号,以免登录混淆
    const COMMON_UC_EXCEPTION_IN_NUMBER_NOT_MOBILE = 'UC_0032';
    // 手机号已绑定
    const COMMON_UC_EXCEPTION_MOBILE_ALREADY_BINDING = 'UC_0033';
    // 可疑账号
    const COMMON_UC_EXCEPTION_ACCOUNT_IS_IN_BLACK = 'UC_0034';
}