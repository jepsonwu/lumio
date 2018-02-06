<?php

namespace Jepsonwu\banyanDB\structures;

use Jepsonwu\banyanDB\traits\SpecialKey;
use Jepsonwu\banyanDB\AbstractBanyan;
use Jepsonwu\banyanDB\SpecialInterfaceBanyan;

/**
 * 键值对 name为key
 *
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:33
 */
class SpecialKeyStructure extends AbstractBanyan implements SpecialInterfaceBanyan
{
    use SpecialKey;

    protected function isSupportMethod($method)
    {
        return false;
    }
}
