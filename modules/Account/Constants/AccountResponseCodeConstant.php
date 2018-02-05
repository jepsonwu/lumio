<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/19
 * Time: 12:31
 */

namespace Modules\Account\Constants;


class AccountResponseCodeConstant
{
    const COMMON_REGISTER_SUCCESS = '0|注册成功';
    const COMMON_ACCOUNT_PASSWORD_SET_SUCCESS = '0|密码设置成功';
    const COMMON_ACCOUNT_PASSWORD_CHANGE_SUCCESS = '0|密码修改成功';
    const COMMON_ACCOUNT_PASSWORD_RESET_SUCCESS = '0|密码重置成功';
    const COMMON_ACCOUNT_THIRD_PARTY_UNBIND_SUCCESS = '0|解绑成功';
    const COMMON_ACCOUNT_MOBILE_BIND_SUCCESS = '0|手机号绑定成功';
    const COMMON_ACCOUNT_MOBILE_CHANGE_SUCCESS = '0|手机号修改成功';
}