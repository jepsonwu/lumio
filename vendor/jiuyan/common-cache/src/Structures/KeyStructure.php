<?php
namespace Jiuyan\CommonCache\Structures;

use Jiuyan\CommonCache\AbstractBanyan;
use Jiuyan\CommonCache\InterfaceBanyan;
use Jiuyan\CommonCache\Traits\Key;

/**
 * 键值对 name为null
 * hash banyan model
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:33
 */
class KeyStructure extends AbstractBanyan implements InterfaceBanyan
{
    use Key;
}