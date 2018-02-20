<?php

namespace Modules\Seller\Constants;

use Jiuyan\Lumio\BanyanDB\BanyanDBFactory;

class SellerBanyanDBConstant
{
    const NAMESPACE_COMMON = "common";

    const TABLE_COMMON_COMMON = "common";

    const COMMON_SELLER_STAT = "seller_stat";

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

    public static function commonSellerStat($userId)
    {
        return self::common(self::getName(self::COMMON_SELLER_STAT, $userId), BanyanDBFactory::HASH_STRUCTURE);
    }
}