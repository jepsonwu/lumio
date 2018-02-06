<?php
/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/8/8
 * Time: 上午10:33
 */

namespace Jiuyan\Request\Tool;

class SignFactory
{
    /**
     * @return SignInterface
     */
    public function make($class)
    {
        return new $class();
    }
}
