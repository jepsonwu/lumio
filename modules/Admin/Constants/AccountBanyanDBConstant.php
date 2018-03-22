<?php

namespace Modules\Admin\Constants;

use Jiuyan\Lumio\BanyanDB\BanyanDBFactory;

class AccountBanyanDBConstant
{
    const NAMESPACE_COMMON = "common";

    const TABLE_COMMON_COMMON = "common";

    const COMMON_ACCOUNT_USER_LOGIN_TOKEN = "account_user_login_token";
    const COMMON_ACCOUNT_USER_INVITE_CODE = "account_user_invite_code";
    const COMMON_ACCOUNT_USER_INVITE_CODE_MAP = "account_user_invite_code_map";

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

    public static function commonAccountUserLoginToken()
    {
        return self::common(self::getName(self::COMMON_ACCOUNT_USER_LOGIN_TOKEN), BanyanDBFactory::HASH_STRUCTURE);
    }

    public static function commonAccountUserInviteCode()
    {
        return self::common(self::getName(self::COMMON_ACCOUNT_USER_INVITE_CODE), BanyanDBFactory::HASH_STRUCTURE);
    }

    public static function commonAccountUserInviteCodeMap()
    {
        return self::common(self::getName(self::COMMON_ACCOUNT_USER_INVITE_CODE_MAP), BanyanDBFactory::HASH_STRUCTURE);
    }
}
