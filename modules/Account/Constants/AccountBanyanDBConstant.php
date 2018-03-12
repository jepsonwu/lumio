<?php

namespace Modules\Account\Constants;

use Jiuyan\Lumio\BanyanDB\BanyanDBFactory;

class AccountBanyanDBConstant
{
    const NAMESPACE_COMMON = "common";

    const TABLE_COMMON_COMMON = "common";

    const COMMON_DEMO = "demo";
    const COMMON_USER_INVITE_CODE_MAP = "user_invite_code_map";
    const COMMON_USER_SMS_CAPTCHA = "user_sms_captcha";

    /**
     *
     * @param $name
     * @param string $prefix
     * @return string
     */
    public static function getName($name, $prefix = "")
    {
        empty($prefix) || $prefix = "_{$prefix}";
        return $name . $prefix;
    }

    public static function common($name, $type = BanyanDBFactory::KEY_STRUCTURE)
    {
        return BanyanDBFactory::getInstance(self::NAMESPACE_COMMON, self::TABLE_COMMON_COMMON, $name, $type);
    }

    public static function commonDemo()
    {
        return self::common(self::COMMON_DEMO, BanyanDBFactory::HASH_STRUCTURE);
    }

    public static function commonUserInviteCodeMap()
    {
        return self::common(self::getName(self::COMMON_USER_INVITE_CODE_MAP), BanyanDBFactory::HASH_STRUCTURE);
    }
}
