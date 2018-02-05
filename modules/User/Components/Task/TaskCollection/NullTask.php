<?php

namespace Modules\User\Components\Task\TaskCollection;

/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 下午12:03
 */
class NullTask extends AbstractTask
{
    public function getType()
    {
        return "null";
    }

    public function getName()
    {
        return "";
    }

    public function getDesc()
    {
        return "";
    }

    public function getIcon()
    {
        return "";
    }

    public function getCoinNumber()
    {
        return 0;
    }

    public function getProtocol()
    {
        return "";
    }

    public function getUserGoldOperation()
    {
        return 0;
    }

    public function getInfo()
    {
        return [];
    }

    public function isFinished()
    {
        return true;
    }

    public function finish()
    {
        return true;
    }
}
