<?php
/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/7/18
 * Time: 上午10:28
 */

namespace Jiuyan\Qconf\Client;

use Jiuyan\Qconf\Client\Contract\QConf;
use ReflectionProperty;
use ReflectionObject;
class JSONObject extends QConf
{
    protected $reflect;
    protected $jsonObject;
    /**
     * @return ReflectionObject
     */
    public function getReflect()
    {
        return $this->reflect;
    }

    /**
     * @param ReflectionObject $reflect
     */
    public function setReflect($reflect)
    {
        $this->reflect = $reflect;
    }
    
    public function __construct($obj)
    {
        $this->jsonObject = $obj;
        $this->reflect = new ReflectionObject($obj);
    }
    public static function  Factory($obj){
        return new self($obj);
    }
    public function getProperties(){
        return $this->reflect->getProperties();
    }
    public function getPropertyValue(ReflectionProperty $pro){
        return $this->jsonObject->{$pro->getName()};
    }
    public function getPropertyName(ReflectionProperty $pro){
        return $pro->getName();
    }
    public function getPropertiesValue(){
        $properties =  $this->reflect->getProperties();
        $values = [];
        foreach ($properties as $p) {
            $values[$this->getPropertyName($p)] = $this->getPropertyValue($p);
        }
        return $values;
    }

    function getConf($path, $default = null, $flag = null)
    {
        $paths = explode('/', trim($path,'/'));
        $obj = null;
        $p = array_shift($paths);
        $obj = $this->jsonObject;
        if (!$p) {
            return '';
        }
        do {
            $obj = $obj->{$p};
        } while ($p = array_shift($paths));
        $gettype = gettype($obj);
        if ($gettype == 'boolean') {
            if ($obj) {
                return '1';
            }else{
                return '0';
            }
        }
        if ($gettype == 'array') {
            return json_encode($obj);
        }
        if ($gettype == 'string' || $gettype == 'int') {
            return $obj;
        }
        return '';
    }

    public function getBatchKeys($path, $default, $flag = null)
    {
        if ($path == '/') {
            $data = $this->getPropertiesValue();
            $array_keys = array_keys($data);
            return $array_keys;
        }

        $paths = explode('/', trim($path,'/'));
        $obj = null;
        $p = array_shift($paths);

        $obj = $this->jsonObject;
        do {
            $obj = $obj->{$p};
        } while ($p = array_shift($paths));
        if (is_object($obj)) {
            $data = JSONObject::Factory($obj)->getPropertiesValue();
            return array_keys($data);
        }else{
            return  [];
        }

        // TODO: Implement getBatchKeys() method.
    }

    public function getBatchConf($path, $default, $flag = null)
    {
        // TODO: Implement getBatchConf() method.
    }

    public function getAllHost($path, $default, $flag = null)
    {
        // TODO: Implement getAllHost() method.
    }

    public function getHost($path, $default, $flag = null)
    {
        // TODO: Implement getHost() method.
    }

}