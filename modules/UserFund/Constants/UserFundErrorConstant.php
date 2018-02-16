<?php

namespace Modules\UserFund\Constants;


class UserFundErrorConstant
{
    const ERR_ACCOUNT_HAS_BEEN_EXISTS = '21101|已经存在该银行卡';
    const ERR_ACCOUNT_CREATE_FAILED = '21102|添加银行卡失败';
    const ERR_ACCOUNT_IS_NOT_ALLOW_CREATE = '21103|不允许添加银行卡';
    const ERR_ACCOUNT_UPDATE_FAILED = '21104|修改银行卡失败';
    const ERR_ACCOUNT_DELETE_FAILED = '21105|删除银行卡失败';
    const ERR_ACCOUNT_INVALID = '21106|无效的账户';
}