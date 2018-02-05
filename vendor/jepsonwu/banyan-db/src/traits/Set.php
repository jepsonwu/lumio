<?php

namespace Jepsonwu\banyanDB\traits;

/**
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/4/13
 * Time: 11:29
 */
trait Set
{
    public function set($key, $value, $expire = null)
    {
        return $this->getBanyan()->zset($this->getName(), $key, $value);
    }

    public function get($key)
    {
        return $this->getBanyan()->zget($this->getName(), $key);
    }

    public function del($key)
    {
        $result = $this->getBanyan()->zdel($this->getName(), $key);
        return $result === false ? false : true;
    }

    public function inc($key, $num = 1)
    {
        return $this->getBanyan()->zincr($this->getName(), $key, $num);
    }

    public function exists($key)
    {
        return (bool)$this->getBanyan()->zexists($this->getName(), $key);
    }

    public function size()
    {
        return (int)$this->getBanyan()->zsize($this->getName());
    }

    public function clear()
    {
        return (bool)$this->getBanyan()->zclear($this->getName());
    }

    public function scan($start, $end, $limit, $keyStart = "")
    {
        $result = $this->getBanyan()->zscan($this->getName(), $keyStart, $start, $end, $limit);
        return empty($result) ? [] : $result;
    }

    public function rScan($start, $end, $limit, $keyStart = "")
    {
        $result = $this->getBanyan()->zrscan($this->getName(), $keyStart, $start, $end, $limit);
        return empty($result) ? [] : $result;
    }
}