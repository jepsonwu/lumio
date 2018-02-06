<?php
/**
 * Created by PhpStorm.
 * User: topone4tvs
 * Date: 2017/12/1
 * Time: 18:10
 */

namespace Jiuyan\Common\Component\InFramework\Libraries\System;

use Illuminate\Support\Collection;

class LumioCollection extends Collection
{
    public function __get($key)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }
        return parent::__get($key);
    }
}