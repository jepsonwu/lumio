<?php

namespace Jepsonwu\banyanDB;

/**
 * banyan interface
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:12
 */
interface InterfaceBanyan
{
    public function getBanyan();

    public function getName();

    public function set($key, $value, $expire = null);

    public function get($key);

    public function del($key);

    public function inc($key, $num = 1);

    public function exists($key);

    public function size();

    public function clear();

    public function scan($start, $end, $limit, $keyStart = "");

    public function rScan($start, $end, $limit, $keyStart = "");
}
