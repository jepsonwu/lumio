<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/10/16
 * Time: 上午11:07
 */

namespace Modules\User\Components\Task\TaskCollection;

interface Task
{
    public function getType();

    public function getName();

    public function getDesc();

    public function getIcon();

    public function getCoinNumber();

    public function getProtocol();

    public function getUserGoldOperation();

    public function isValid();

    public function getInfo();

    public function finish();

    public function isFinished();
}
