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
    const NAMESPACE_COMMON = "common";

    const TABLE_COMMON_COMMON = "common";

    const COMMON_DEMO = "demo";

    public static function common($name, $type = BanyanDBFactory::KEY_STRUCTURE)
    {
        return BanyanDBFactory::getInstance(self::NAMESPACE_COMMON, self::TABLE_COMMON_COMMON, $name, $type);
    }

    public static function commonDemo()
    {
        return self::common(self::COMMON_DEMO, BanyanDBFactory::HASH_STRUCTURE);
    }
}
