<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/10/13
 * Time: 10:38
 */

namespace Modules\Account\Constants;

use Jiuyan\Lumio\BanyanDB\BanyanDBFactory;

class AccountBanyanDBConstant
{
    const NAMESPACE_IN_SMS = 'in_sms';
    const NAMESPACE_IN_COMMON = 'in_common';

    const TABLE_FOR_IN_SMS_AUTH_CODE = 'auth_code';
    const TABLE_FOR_IN_ACCOUNT_COMMON = 'in_account';

    const SET_FOR_ACCOUNT_PASSWORD_RESET_TIMES = 'account_password_reset_time';

    /**
     * @param $name
     * @param $type
     * @return \Jiuyan\CommonCache\InterfaceBanyan|mixed
     */
    public static function smsCommon($name, $type = BanyanDBFactory::KEY_STRUCTURE)
    {
        return BanyanDBFactory::getInstance(self::NAMESPACE_IN_SMS, self::TABLE_FOR_IN_SMS_AUTH_CODE, $name, $type);
    }

    /**
     * @param $name
     * @param int $type
     * @return \Jiuyan\CommonCache\InterfaceBanyan|mixed
     */
    public static function accountCommon($name, $type = BanyanDBFactory::HASH_STRUCTURE)
    {
        return BanyanDBFactory::getInstance(self::NAMESPACE_IN_COMMON, self::TABLE_FOR_IN_ACCOUNT_COMMON, $name, $type);
    }

    /**
     * @return \Jiuyan\CommonCache\InterfaceBanyan|mixed
     */
    public static function accountPasswordResetTimes()
    {
        return self::smsCommon(self::SET_FOR_ACCOUNT_PASSWORD_RESET_TIMES, BanyanDBFactory::KEY_STRUCTURE);
    }
}
