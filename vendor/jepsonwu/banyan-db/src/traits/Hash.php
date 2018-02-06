<?php

namespace Jepsonwu\banyanDB\traits;

/**
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/4/13
 * Time: 11:31
 */
trait Hash
{
    public function set($key, $value, $expire = null)
    {
        return $this->getBanyan()->hset($this->getName(), $key, $value);
    }

    public function get($key)
    {
        return $this->getBanyan()->hget($this->getName(), $key);
    }

    public function del($key)
    {
        $result = $this->getBanyan()->hdel($this->getName(), $key);
        return $result === false ? false : true;
    }

    public function inc($key, $num = 1)
    {
        return $this->getBanyan()->hincr($this->getName(), $key, $num);
    }

    public function exists($key)
    {
        return (bool)$this->getBanyan()->hexists($this->getName(), $key);
    }

    public function size()
    {
        return (int)$this->getBanyan()->hsize($this->getName());
    }

    public function clear()
    {
        return (bool)$this->getBanyan()->hclear($this->getName());
    }

    public function scan($start, $end, $limit, $keyStart = "")
    {
        $result = $this->getBanyan()->hscan($this->getName(), $start, $end, $limit);
        return empty($result) ? [] : $result;
    }

    public function rScan($start, $end, $limit, $keyStart = "")
    {
        $result = $this->getBanyan()->hrscan($this->getName(), $start, $end, $limit);
        return empty($result) ? [] : $result;
    }
}