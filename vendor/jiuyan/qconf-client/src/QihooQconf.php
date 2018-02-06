<?php
namespace Jiuyan\Qconf\Client;

use Jiuyan\Qconf\Client\Contract\QConf as BaseQconf;
use Qconf;
class QihooQconf extends BaseQconf{

    public function getConf($path, $default = null, $flag = null)
    {
       return  Qconf::getConf($path, $this->getIdc(), $flag);
    }

    public function getBatchKeys($path, $default, $flag = null)
    {
        return  Qconf::getBatchKeys($path, $this->getIdc(), $flag);
    }

    public function getBatchConf($path, $default, $flag = null)
    {
        return Qconf::getBatchConf($path, $this->getIdc(), $flag);

    }

    public function getAllHost($path, $default, $flag = null)
    {
        return Qconf::getAllHost($path, $this->getIdc(), $flag);
    }

    public function getHost($path, $default, $flag = null)
    {
        return Qconf::getHost($path, $this->getIdc(), $flag);
    }
}
