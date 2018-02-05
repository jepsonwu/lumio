<?php

namespace Jepsonwu\banyanDB\iterators;

/**
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/7/11
 * Time: 15:26
 */
class RScanIterator extends ScanIterator
{
    protected function scan()
    {
        return $this->getBanyan()->rScan($this->scan_start, "", $this->limit, $this->key_start);
    }
}
