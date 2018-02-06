<?php

namespace Jepsonwu\banyanDB\structures;

use Jepsonwu\banyanDB\traits\Hash;
use Jepsonwu\banyanDB\AbstractBanyan;
use Jepsonwu\banyanDB\InterfaceBanyan;

/**
 * @method hgetall() array|mixed
 * @method multi_hget() array|mixed
 * @method multi_hset() array|mixed
 * @method multi_hdel() array|mixed
 *
 *
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:33
 */
class HashStructure extends AbstractBanyan implements InterfaceBanyan
{
    use Hash;

    private $supportMethod = [
        'hgetall',
        'multi_hget',
        'multi_hset',
        'multi_hdel'
    ];


    protected function isSupportMethod($method)
    {
        return in_array($method, $this->supportMethod);
    }
}
