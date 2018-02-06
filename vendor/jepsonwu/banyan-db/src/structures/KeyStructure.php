<?php

namespace Jepsonwu\banyanDB\structures;

use Jepsonwu\banyanDB\traits\Key;
use Jepsonwu\banyanDB\AbstractBanyan;
use Jepsonwu\banyanDB\InterfaceBanyan;

/**
 * 键值对 name为null
 *
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:33
 */
class KeyStructure extends AbstractBanyan implements InterfaceBanyan
{
    use Key;

    protected function isSupportMethod($method)
    {
        return false;
    }
}
