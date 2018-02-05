<?php

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 上午11:27
 */

namespace Modules\User\Components\Task;


use Modules\User\Components\Task\TaskCollection\Task;

class TaskFactory
{
    //task type
    const TASK_EDIT_USER_AVATAR = "task_edit_avatar";
    const TASK_EDIT_USER_NAME = "task_edit_name";
    const TASK_EDIT_USER_BIRTHDAY = "task_edit_birthday";
    const TASK_EDIT_USER_SCHOOL = "task_edit_school";

    const TASK_BIND_WEIBO = "task_bind_weibo";
    const TASK_BIND_WEIXIN = "task_bind_weixin";
    const TASK_BIND_QQ = "task_bind_qq";

    const TASK_FIRST_PUBLISH_PHOTO = "task_first_send_photo";
    const TASK_FIRST_SIGN = "task_first_sign";
    const TASK_FIRST_WATCH = "task_first_watch";
    const TASK_FIRST_ZAN = "task_first_zan";

    private static $instance;

    /**
     * @param $type
     * @param $userId
     * @return Task
     */
    public static function getInstance($type, $userId)
    {
        $key = md5($type . $userId);

        if (!isset(self::$instance[$key])) {
            $className = "Modules\\User\\Components\\Task\\TaskCollection\\{$type}Task";

            $task = self::getTask($className, $userId);
            $task->isValid() || $task = self::nullTask();
            self::$instance[$key] = $task;
        }

        return self::$instance[$key];
    }

    /**
     * @param $className
     * @return Task
     */
    private static function getTask($className, $userId)
    {
        return new $className($userId);
    }

    private static function nullTask()
    {
        return self::getInstance("Null", 0);
    }

    public static function editAvatar($userId)
    {
        return self::getInstance("EditAvatar", $userId);
    }

    public static function editBirthday($userId)
    {
        return self::getInstance("EditBirthday", $userId);
    }

    public static function editName($userId)
    {
        return self::getInstance("EditName", $userId);
    }

    public static function editSchool($userId)
    {
        return self::getInstance("EditSchool", $userId);
    }

    public static function editPersonalTag($userId)
    {
        return self::getInstance("EditPersonalTag", $userId);
    }

    public static function editNumber($userId)
    {
        return self::getInstance("EditNumber", $userId);
    }

    public static function editAddress($userId)
    {
        return self::getInstance("EditAddress", $userId);
    }

    public static function bindWeibo($userId)
    {
        return self::getInstance("BindWeibo", $userId);
    }

    public static function bindWeixin($userId)
    {
        return self::getInstance("BindWeixin", $userId);
    }

    public static function bindQq($userId)
    {
        return self::getInstance("BindQq", $userId);
    }

    public static function firstPublishPhoto($userId)
    {
        return self::getInstance("FirstPublishPhoto", $userId);
    }

    public static function firstSign($userId)
    {
        return self::getInstance("FirstSign", $userId);
    }

    public static function firstWatch($userId)
    {
        return self::getInstance("FirstWatch", $userId);
    }

    public static function firstZan($userId)
    {
        return self::getInstance("FirstZan", $userId);
    }

    public static function auth($userId)
    {
        return self::getInstance("Auth", $userId);
    }

    public static function uploadContact($userId)
    {
        return self::getInstance("UploadContact", $userId);
    }
}
