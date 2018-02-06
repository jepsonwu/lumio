<?php

namespace Jepsonwu\banyanDB\structures;

use Jepsonwu\banyanDB\SpecialInterfaceBanyan;
use Jepsonwu\banyanDB\AbstractBanyan;
use Jepsonwu\banyanDB\traits\Queue;

/**
 * @method qlist() array|mixed
 * @method qclear() bool
 * @method qback() array|mixed
 * @method qpush_back() array|mixed
 *
 *
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:33
 */
class QueueStructure extends AbstractBanyan implements SpecialInterfaceBanyan
{
    use Queue;

    private $supportMethod = [
        'qlist',
        'qclear',
        'qback',
        'qpush_back'
    ];


    protected function isSupportMethod($method)
    {
        return in_array($method, $this->supportMethod);
    }
}
