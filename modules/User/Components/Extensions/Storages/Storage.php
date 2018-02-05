<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/1
 * Time: 下午3:49
 */

namespace Modules\User\Components\Extensions\Storages;

interface Storage
{
    public function set($userId, $name, $value);

    public function get($userId, $name);
}