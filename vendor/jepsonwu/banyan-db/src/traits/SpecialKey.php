<?php

namespace Jepsonwu\banyanDB\traits;

/**
 * 键值对特殊实现方式 name为key
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/4/13
 * Time: 11:31
 */
trait SpecialKey
{
    public function set($value, $expire = null)
    {
        return is_null($expire) ? $this->getBanyan()->set($this->getName(), $value) :
            $this->getBanyan()->setx($this->getName(), $value, $expire);
    }

    public function get()
    {
        return $this->getBanyan()->get($this->getName());
    }

    public function del()
    {
        $result = $this->getBanyan()->del($this->getName());
        return $result === false ? false : true;
    }

    public function inc($num = 1)
    {
        return $this->getBanyan()->incr($this->getName(), $num);
    }

    public function exists()
    {
        return (bool)$this->getBanyan()->exists($this->getName());
    }

    public function size()
    {
        return 0;
        //return (int)$this->getBanyan()->strlen($this->getName()); //todo banyan not support
    }

    public function clear()
    {
        return $this->del();
    }
}
