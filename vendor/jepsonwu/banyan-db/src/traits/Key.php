<?php

namespace Jepsonwu\banyanDB\traits;

/**
 * 键值对 name为null
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/4/13
 * Time: 11:31
 */
trait Key
{
    public function set($key, $value, $expire = null)
    {
        return is_null($expire) ? $this->getBanyan()->set($key, $value) :
            $this->getBanyan()->setx($key, $value, $expire);
    }

    public function get($key)
    {
        return $this->getBanyan()->get($key);
    }

    public function del($key)
    {
        $result = $this->getBanyan()->del($key);
        return $result === false ? false : true;
    }

    public function inc($key, $num = 1)
    {
        return $this->getBanyan()->incr($key, $num);
    }

    public function exists($key)
    {
        return (bool)$this->getBanyan()->exists($key);
    }

    public function size()
    {
        return true;
    }

    public function clear()
    {
        return true;
    }

    public function scan($start, $end, $limit, $keyStart = "")
    {
        return [];
    }

    public function rScan($start, $end, $limit, $keyStart = "")
    {
        return [];
    }
}