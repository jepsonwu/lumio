<?php

namespace Jepsonwu\banyanDB;

use \Exception;

/**
 * support redis mongodb
 * Created by PhpStorm.
 * User: jepsonwu
 * Date: 2017/6/19
 * Time: 15:09
 */
abstract class AbstractBanyan
{
    private $banyan;

    private $namespace;
    private $table;
    private $name;

    private $generateBanyanCallback;

    public function __construct($namespace, $table, $name = null)
    {
        $this->namespace = $namespace;
        $this->table = $table;
        $this->name = $name;
    }

    public function setGenerateBanyanCallback($callback)
    {
        $this->generateBanyanCallback = $callback;
        return $this;
    }

    public function getBanyan()
    {
        if (is_null($this->generateBanyanCallback)) {
            throw new Exception("generate banyan callback invalid");
        }

        is_null($this->banyan) &&
        $this->banyan = call_user_func_array($this->generateBanyanCallback, [$this->namespace, $this->table]);
        return $this->banyan;
    }

    public function getName()
    {
        return $this->name;
    }

    abstract protected function isSupportMethod($method);

    public function __call($name, $arguments)
    {
        if ($this->isSupportMethod($name)) {
            array_unshift($arguments, $this->getName());
            return call_user_func_array(
                [$this->getBanyan(), $name],
                $arguments
            );
        }
        return false;
    }
}
