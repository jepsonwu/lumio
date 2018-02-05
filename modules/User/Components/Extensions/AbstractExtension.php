<?php
/**
 * Created by PhpStorm.
 * User: shanzha
 * Date: 2017/12/4
 * Time: 下午4:20
 */

namespace Modules\User\Components\Extensions;

use Modules\User\Components\Extensions\Storages\Storage;
use \Exception;

abstract class AbstractExtension
{
    /**
     * @var Storage
     */
    private static $storage;

    private $userId;

    protected $prefix = "";

    /**
     * @var array Column=>key
     */
    protected $propertyMap = [];

    public function __construct($userId)
    {
        $this->setUserId($userId);
        $this->setPrefix();
    }

    protected function setUserId($userId)
    {
        $this->userId = $userId;
    }

    protected function getUserId()
    {
        return $this->userId;
    }

    protected function setPrefix()
    {
        $class = get_called_class();
        $this->prefix || $this->prefix = substr($class, strrpos($class, "\\") + 1);
    }

    public static function registerStorage(Storage $storage)
    {
        self::$storage = $storage;
    }

    protected function getStorage()
    {
        if (is_null(self::$storage)) {
            throw new Exception("invalid storage");
        }

        return self::$storage;
    }

    public function __get($name)
    {
        return $this->isValidProperty($name) ? $this->get($this->getPropertyKey($name)) : false;
    }

    public function __set($name, $value)
    {
        return $this->isValidProperty($name) ? $this->set($this->getPropertyKey($name), $value) : false;
    }

    protected function isValidProperty($name)
    {
        return isset($this->propertyMap[$name]);
    }

    protected function getPropertyKey($name)
    {
        return $this->prefix . '_' . $this->propertyMap[$name];
    }

    protected function set($name, $value)
    {
        return $this->getStorage()->set($this->getUserId(), $name, $value);
    }

    protected function get($name)
    {
        return $this->getStorage()->get($this->getUserId(), $name);
    }
}