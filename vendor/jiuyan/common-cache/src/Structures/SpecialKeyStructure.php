<?php
namespace Jiuyan\CommonCache\Structures;

use Jiuyan\CommonCache\AbstractBanyan;
use Jiuyan\CommonCache\SpecialInterfaceBanyan;
use Jiuyan\CommonCache\Traits\SpecialKey;

/**
 * 键值对 name为key
 * hash banyan model
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:33
 */
class SpecialKeyStructure extends AbstractBanyan implements SpecialInterfaceBanyan
{
    use SpecialKey;
}