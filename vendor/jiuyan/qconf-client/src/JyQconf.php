<?php
namespace Jiuyan\Qconf\Client;


use Jiuyan\Qconf\Client\Contract\QConf;

class JyQconf {
    protected static $instance;
    /**
     * @var QConf
     */
    protected $qconf = null;

    /**
     * @return bool
     */
    public function isInstallQconf()
    {
        return    class_exists('QConf', false);
    }



    public function __construct()
    {

    }


    /**
     * @return array|null
     */
    public function getConfig()
    {
        return $this->qconf->getConfig();
    }

    /**
     * @param array|null $config
     */
    public function setConfig($config)
    {
        return $this->qconf->setConfig($config);
    }

    /**
     * @return null
     */
    public function getIdc()
    {
        return $this->qconf->getIdc();
    }

    /**
     * @param null $idc
     */
    public function setIdc($idc)
    {
        $this->qconf->setIdc($idc);
    }

    public function getConf($path, $default = null, $flag = null)
    {
        $data = $this->qconf->getConf($path, $default, $flag);
        if (!$data) {
            $data = $default;
        }
        return $data;
    }

    /**
     * @return QConf
     */
    public function getQconf()
    {
        return $this->qconf;
    }

    /**
     * @param QConf $qconf
     */
    public function setQconf(QConf $qconf)
    {
        $this->qconf = $qconf;
    }

    public function getBatchKeys($path, $default, $flag = null)
    {
        $data = $this->qconf->getBatchKeys($path, $default, $flag);
        if (!$data) {
            $data = $default;
        }
        return $data;
    }

    public function getBatchConf($path, $default, $flag = null)
    {
        $data = $this->qconf->getBatchConf($path, $default, $flag);
        if (!$data) {
            $data = $default;
        }
        return $data;

    }

    public function getAllHost($path, $default, $flag = null)
    {
        $data = $this->qconf->getAllHost($path, $default, $flag);
        if (!$data) {
            $data = $default;
        }
        return $data;

    }

    public function getHost($path, $default, $flag = null)
    {
        $data = $this->qconf->getHost($path, $default, $flag);
        if (!$data) {
            $data = $default;
        }
        return $data;
    }
}
