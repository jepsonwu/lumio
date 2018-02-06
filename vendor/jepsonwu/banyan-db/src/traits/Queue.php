<?php

namespace Jepsonwu\banyanDB\traits;

/**
 *
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/4/13
 * Time: 11:31
 */
trait Queue
{
    public function set($value, $expire = null)
    {
        return $this->getBanyan()->qpush_front($this->getName(), (array)$value);
    }

    public function get()
    {
        return $this->getBanyan()->qfront($this->getName());
    }

    public function del()
    {
        $this->get();
        return true;
    }

    public function inc($num = 1)
    {
        return false;
    }

    public function exists()
    {
        $latest = $this->get();
        return is_null($latest) ? false : $latest;
    }

    public function size()
    {
        return (int)$this->getBanyan()->qsize($this->getName());
    }

    public function clear()
    {
        return (bool)$this->getBanyan()->qclear($this->getName());
    }
}
