<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/11/29
 * Time: 21:02
 */

namespace Modules\User\Constants;

use Jepsonwu\banyanDB\BanyanFactory;

use Jepsonwu\banyanDB\InterfaceBanyan;
use Jiuyan\Lumio\BanyanDB\BanyanDBFactory;

class UserBanyanDBConstant
{
    const NAMESPACE_USER_TASK = "in_user_task";
    const NAMESPACE_USER_COUNTER = 'in_user_count';

    const TABLE_USER_TASK = 'task';
    const TABLE_USER_COUNTER_PHOTO = 'user_photo_count';

    const MAP_NEW_USER_GUIDE = 'new_user_guide_push_images';
    const MAP_NEW_USER_POOL = 'task_real_time_new_fish_pool';

    //user counter
    const USER_COUNTER_GOLD_INFO = 'gold_info';

    //task
    const USER_TASK_STATUS = 'task';

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

    /**
     * @param $name
     * @param $type
     * @return \Jiuyan\CommonCache\InterfaceBanyan|mixed
     */
    public static function common($name, $type = BanyanFactory::KEY_STRUCTURE)
    {
        return BanyanFactory::getInstance(self::NAMESPACE_USER_TASK, self::TABLE_USER_TASK, $name, $type);
    }

    /**
     * @return \Jiuyan\CommonCache\InterfaceBanyan|mixed
     */
    public static function newUserGuide()
    {
        return self::common(self::MAP_NEW_USER_GUIDE, BanyanFactory::HASH_STRUCTURE);
    }

    /**
     * @return \Jiuyan\CommonCache\InterfaceBanyan|mixed
     */
    public static function newUserPool()
    {
        return self::common(self::MAP_NEW_USER_POOL, BanyanFactory::HASH_STRUCTURE);
    }


    public static function userCounterPhoto($name = null, $type = BanyanDBFactory::KEY_STRUCTURE)
    {
        return BanyanFactory::getInstance(
            self::NAMESPACE_USER_COUNTER,
            self::TABLE_USER_COUNTER_PHOTO,
            $name,
            $type,
            "NServiceFactory::BanyanDB"
        );
    }

    /**
     * @param $userId
     * @return InterfaceBanyan
     */
    public static function userCounterGoldInfo($userId)
    {
        return self::userCounterPhoto(
            self::getName(self::USER_COUNTER_GOLD_INFO, $userId), BanyanFactory::HASH_STRUCTURE
        );
    }

    public static function userTask($name, $type = BanyanFactory::KEY_STRUCTURE)
    {
        return BanyanFactory::getInstance(self::NAMESPACE_USER_TASK, self::TABLE_USER_TASK, $name, $type);
    }

    /**
     * @param $userId
     * @return InterfaceBanyan
     */
    public static function userTaskStatus($userId)
    {
        return self::userTask(
            self::getName(self::USER_TASK_STATUS, $userId), BanyanFactory::HASH_STRUCTURE
        );
    }
}