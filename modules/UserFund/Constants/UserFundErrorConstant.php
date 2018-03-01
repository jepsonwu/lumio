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
    const ERR_ACCOUNT_NO_DEPLOY = '21107|未添加银行卡';
    const ERR_ACCOUNT_OPERATE_ILLEGAL = '21108|非法操作';

    const ERR_WALLET_RECHARGE_FAILED = "21201|充值失败";
    const ERR_WALLET_INVALID_RECHARGE = "21202|无效的充值记录";
    const ERR_WALLET_DISALLOW_CLOSE_RECHARGE = "21202|不允许关闭充值记录";
    const ERR_WALLET_DISALLOW_VERIFY_RECHARGE = "21202|不允许审核充值记录";
    const ERR_WALLET_WITHDRAW_FAILED = "21202|提现失败";
    const ERR_WALLET_INVALID_WITHDRAW = "21202|无效的提现记录";
    const ERR_WALLET_DISALLOW_CLOSE_WITHDRAW = "21202|不允许关闭提现记录";
    const ERR_WALLET_DISALLOW_VERIFY_WITHDRAW = "21202|不允许审核提现记录";
    const ERR_WALLET_OPERATE_ILLEGAL = "21202|非法操作";
    const ERR_WALLET_CLOSE_WITHDRAW_FAILED = "21202|关闭提现记录失败";
    const ERR_WALLET_INVALID_CAPTCHA = "21202|验证码错误";
    const ERR_WALLET_INVALID_RECORD = "21203|无效的资金记录";
    const ERR_WALLET_VERIFY_RECHARGE_FAILED = "21204|审核充值失败";
    const ERR_WALLET_VERIFY_WITHDRAW_FAILED = "21205|审核提现失败";
    const ERR_WALLET_INSUFFICIENT_BALANCE = "21206|余额不足";
    const ERR_WALLET_INSUFFICIENT_LOCKED = "21207|意外错误";
}