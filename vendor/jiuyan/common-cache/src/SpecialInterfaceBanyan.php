<?php
namespace Jiuyan\CommonCache;
/**
 *
 * special banyan interface
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:12
 */
interface SpecialInterfaceBanyan
{
    public function set($value, $expire = null);

    public function get();

    public function del();

    public function inc($num = 1);

    public function exists();

    public function size();
}