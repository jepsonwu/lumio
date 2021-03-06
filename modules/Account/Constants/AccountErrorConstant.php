<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/10
 * Time: 10:24
 */

namespace Modules\Account\Constants;


class AccountErrorConstant
{
    const ERR_ACCOUNT_USER_NOT_EXISTS = '20101|用户不存在';
    const ERR_ACCOUNT_USER_ACCOUNT_PASSWORD_WRONG = '20102|账号或密码不正确';
    const ERR_ACCOUNT_PASSWORD_FORMAT_INVALID = '20103|密码格式有误';
    const ERR_ACCOUNT_USER_EXISTS = '20104|已经存在该用户';
    const ERR_ACCOUNT_INVALID_SMS_CODE = '20105|验证码错误或者已过期';
    const ERR_ACCOUNT_REGISTER_FAILED = '20106|注册失败';
    const ERR_ACCOUNT_SMS_CODE_SEND_FAILED = '20107|短信验证码发送失败';
    const ERR_ACCOUNT_PASSWORD_RESET_FAILED = '20108|重置密码失败';
    const ERR_ACCOUNT_PASSWORD_CHANGE_FAILED = '20109|修改密码失败';
    const ERR_ACCOUNT_PASSWORD_SAME_NEW_PASSWORD = '20110|新旧密码一样';
    const ERR_ACCOUNT_SMS_CAPTCHA_TOO_FREQUENTLY = '20111|验证码发送过于频繁';
    const ERR_ACCOUNT_AUTHORIZED_FAILED = '20112|身份验证失败';
    const ERR_ACCOUNT_LOGIN_FAILED = '20113|登录失败';
}