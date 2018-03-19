<?php

namespace Modules\Task\Constants;

use Jiuyan\Lumio\BanyanDB\BanyanDBFactory;

class TaskBanyanDBConstant
{
    const NAMESPACE_COMMON = "common";

    const TABLE_COMMON_COMMON = "common";

    const COMMON_TASK_ORDER_USER_LATEST_RECORD = "task_order_user_latest_record";

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

    public static function commonTaskOrderUserLatestRecord($userId)
    {
        return self::common(self::getName(self::COMMON_TASK_ORDER_USER_LATEST_RECORD, $userId), BanyanDBFactory::HASH_STRUCTURE);
    }
}
