<?php

namespace Jepsonwu\banyanDB\structures;

use Jepsonwu\banyanDB\traits\Set;
use Jepsonwu\banyanDB\AbstractBanyan;
use Jepsonwu\banyanDB\InterfaceBanyan;

/**
 * @method multi_zset() array|mixed
 * @method multi_zget() array|mixed
 * @method multi_zdel() bool
 * @method zrank() array|mixed
 * @method zrrank() array|mixed
 *
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:33
 */
class SetStructure extends AbstractBanyan implements InterfaceBanyan
{
    use Set;

    private $supportMethod = [
        'multi_zset',
        'multi_zget',
        'multi_zdel',
        'zrank',
        'zrrank'
    ];

    protected function isSupportMethod($method)
    {
        return in_array($method, $this->supportMethod);
    }
}
