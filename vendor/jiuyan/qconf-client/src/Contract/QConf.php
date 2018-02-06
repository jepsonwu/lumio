<?php
namespace Jiuyan\Qconf\Client\Contract;
/**
 * Created by PhpStorm.
 * User: xinghuo
 * Date: 2017/6/5
 * Time: 上午10:22
 */
abstract class QConf
{
    private $config = null;
    protected $idc = null;

    public function __construct($idc = '', array $config = [])
    {
        $this->config = $config;
        $this->setIdc($idc);
    }

    /**
     * @return array|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array|null $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return null
     */
    public function getIdc()
    {
        return $this->idc;
    }

    /**
     * @param null $idc
     */
    public function setIdc($idc)
    {
        $this->idc = $idc;
    }

    abstract function getConf($path, $default = null, $flag = null);

    abstract public function getBatchKeys($path, $default, $flag = null);

    public abstract function getBatchConf($path, $default, $flag = null);

    public abstract function getAllHost($path, $default, $flag = null);

    public abstract function getHost($path, $default, $flag = null);
}