<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/1
 * Time: 下午4:54
 */

namespace Modules\User\Components\Extensions\Storages;

use Jiuyan\CommonCache\InterfaceBanyan;
use Jiuyan\Lumio\BanyanDB\BanyanDBFactory;

class BanyanDBStorage implements Storage
{
    public function get($userId, $name)
    {
        return $this->getBanyan($userId)->get($name);
    }

    public function set($userId, $name, $value)
    {
        return $this->getBanyan($userId)->set($name, $value);
    }

    /**
     * @param $userId
     * @return Mixed|InterfaceBanyan
     */
    protected function getBanyan($userId)
    {
        $config = config("user.extensions_storage.config");
        $namespace = $config['namespace'];
        $table = $config['table'];
        $name = $config['name'] . "_{$userId}";

        return BanyanDBFactory::getInstance($namespace, $table, $name, BanyanDBFactory::HASH_STRUCTURE);
    }
}