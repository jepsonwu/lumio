<?php
namespace Jiuyan\CommonCache\Structures;

use Jiuyan\CommonCache\AbstractBanyan;
use Jiuyan\CommonCache\InterfaceBanyan;
use Jiuyan\CommonCache\Traits\Hash;

/**
 * hash banyan model
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:33
 */
class HashStructure extends AbstractBanyan implements InterfaceBanyan
{
    use Hash;
}